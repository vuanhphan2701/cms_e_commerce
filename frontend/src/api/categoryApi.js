import axios from "axios";

export async function getCategories() {
  const res = await axios.get('http://localhost:8000/api/category');
  return res.data.data;
}

export async function getCategoryById(id) {
  const res = await axios.get(`${BASE_URL}/categories/${id}`);
  return res.data.data;
}
