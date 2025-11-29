import axios from "axios";

export async function getBrands() {
  const res = await axios.get('http://localhost:8000/api/brand');
  return res.data.data; // hoặc res.data nếu backend bạn khác
}

export async function getBrandById(id) {
  const res = await axios.get(`${BASE_URL}/brands/${id}`);
  return res.data.data;
}
