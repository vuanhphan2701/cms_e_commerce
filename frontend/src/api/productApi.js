import axios from "./axios";

export async function getAllProducts({ page = 1, limit = 10, sortBy = "id", order = "desc", include = "" }) {
  const res = await axios.get('http://localhost:8000/api/product', { params: { page, limit, sortBy, order, include } });
  // Trả array + meta
  return {
    items: res.data.data.data,
    meta: res.data.data.meta,
  };
} 

export const getProductById = async (id) => {
  const res = await axios.get(`/product/${id}`);
  return res.data.data;
}

export const createProduct = async (product) => {
  const res = await axios.post("/product", product);
  return res.data.data;
}
export const updateProduct = async (id, data) => {
  try {
    return await axios.put(`/product/${id}`, data);
  } catch (error) {
    if (error.response?.status === 409) {
      throw new Error("CONFLICT");
    }
    throw error;
  }
};

export const deleteProduct = async (id) => {
  await axios.delete(`/product/${id}`);
  return true;
}

