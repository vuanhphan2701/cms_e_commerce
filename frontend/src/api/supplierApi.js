import axios from "axios";

export async function getSuppliers() {
  const res = await axios.get('http://localhost:8000/api/supplier');
  return res.data.data;
}

export async function getSupplierById(id) {
  const res = await axios.get(`${BASE_URL}/suppliers/${id}`);
  return res.data.data;
}
