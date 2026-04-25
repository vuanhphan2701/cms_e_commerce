import api from "./axios";

export async function getAllSuppliers(options = {}) {
  const res = await api.get("supplier", { params: options });
  return {
    items: res.data.data.data,
    meta: res.data.data.meta || res.data.data,
  };
}

export async function getSuppliers() {
  const res = await api.get("supplier");
  return res.data.data;
}

export async function getSupplierById(id) {
  const res = await api.get(`supplier/${id}`);
  return res.data.data;
}

export async function createSupplier(data) {
  const res = await api.post("supplier", data);
  return res.data.data;
}

export async function updateSupplier(id, data) {
  const res = await api.put(`supplier/${id}`, data);
  return res.data.data;
}

export async function deleteSupplier(id) {
  await api.delete(`supplier/${id}`);
  return true;
}
