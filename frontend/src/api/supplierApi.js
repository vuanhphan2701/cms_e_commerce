import api from "./axios";

export async function getSuppliers() {
  const res = await api.get("supplier");
  return res.data.data;
}

export async function getSupplierById(id) {
  const res = await api.get(`/suppliers/${id}`);
  return res.data.data;
}
