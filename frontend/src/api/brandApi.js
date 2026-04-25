import api from "./axios";

export async function getAllBrands(options = {}) {
  const res = await api.get("brand", { params: options });
  return {
    items: res.data.data.data,
    meta: res.data.data.meta || res.data.data, // Fallback in case backend doesn't wrap paginator in meta
  };
}

export async function getBrands() {
  const res = await api.get("brand");
  return res.data.data;
}

export async function getBrandById(id) {
  const res = await api.get(`brand/${id}`);
  return res.data.data;
}

export async function createBrand(data) {
  const res = await api.post("brand", data);
  return res.data.data;
}

export async function updateBrand(id, data) {
  const res = await api.put(`brand/${id}`, data);
  return res.data.data;
}

export async function deleteBrand(id) {
  await api.delete(`brand/${id}`);
  return true;
}
