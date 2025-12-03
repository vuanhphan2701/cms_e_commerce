import api from "./axios";

export async function getBrands() {
  const res = await api.get("brand");
  return res.data.data; // hoặc res.data nếu backend bạn khác
}

export async function getBrandById(id) {
  const res = await api.get(`/brands/${id}`);
  return res.data.data;
}
