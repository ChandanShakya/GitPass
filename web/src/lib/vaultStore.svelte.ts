import { api } from "./api";
import { session } from "./session.svelte";
import { encryptEntry, decryptEntry, encryptMessage, type EntryFields } from "./vaultCrypto";

export interface DecryptedEntry extends EntryFields {
  id: string;
  category: string;
  favorite: boolean;
  version: number;
  updatedAt: number;
}

interface ServerItem {
  id: string;
  category: string;
  favorite: number;
  wrapped_row_key: string;
  title_blob: string;
  body_blob: string;
  current_version: number;
  updated_at: number;
  deleted_at: number | null;
}

let entries = $state<DecryptedEntry[]>([]);
let loaded = $state(false);
let loading = $state(false);

async function decryptItem(item: ServerItem): Promise<DecryptedEntry> {
  const fields = await decryptEntry(session.userId!, item.id, session.dek!, {
    wrappedRowKey: item.wrapped_row_key,
    titleBlob: item.title_blob,
    bodyBlob: item.body_blob,
  });
  return { id: item.id, category: item.category, favorite: !!item.favorite, version: item.current_version, updatedAt: item.updated_at, ...fields };
}

export const vaultStore = {
  get entries() {
    return entries;
  },
  get loaded() {
    return loaded;
  },
  get loading() {
    return loading;
  },
  get favorites() {
    return entries.filter((e) => e.favorite);
  },

  async load() {
    if (!session.isUnlocked) return;
    loading = true;
    try {
      const res = await api.get<{ items: ServerItem[] }>("/vault/items?since=0");
      const live = res.items.filter((i) => !i.deleted_at);
      entries = await Promise.all(live.map(decryptItem));
      loaded = true;
    } finally {
      loading = false;
    }
  },

  async create(fields: EntryFields): Promise<string> {
    const id = crypto.randomUUID();
    const enc = await encryptEntry(session.userId!, id, session.dek!, fields);
    const messageBlob = await encryptMessage(session.userId!, id, session.dek!, "Created entry", 1);
    await api.post("/vault/items", {
      id,
      category: "login",
      wrappedRowKey: enc.wrappedRowKey,
      titleBlob: enc.titleBlob,
      bodyBlob: enc.bodyBlob,
      messageBlob,
      deviceName: navigator.userAgent.slice(0, 40),
    });
    entries = [{ id, category: "login", favorite: false, version: 1, updatedAt: Date.now(), ...fields }, ...entries];
    return id;
  },

  async update(id: string, fields: EntryFields, message: string) {
    const current = entries.find((e) => e.id === id);
    if (!current) throw new Error("entry not found locally");
    const enc = await encryptEntry(session.userId!, id, session.dek!, fields);
    const newVersion = current.version + 1;
    const messageBlob = await encryptMessage(session.userId!, id, session.dek!, message, newVersion);
    await api.patch(`/vault/items/${id}`, { titleBlob: enc.titleBlob, bodyBlob: enc.bodyBlob, messageBlob, deviceName: navigator.userAgent.slice(0, 40) });
    entries = entries.map((e) => (e.id === id ? { ...e, ...fields, version: newVersion, updatedAt: Date.now() } : e));
  },

  async remove(id: string) {
    await api.del(`/vault/items/${id}`);
    entries = entries.filter((e) => e.id !== id);
  },

  find(id: string) {
    return entries.find((e) => e.id === id);
  },

  reset() {
    entries = [];
    loaded = false;
  },
};
