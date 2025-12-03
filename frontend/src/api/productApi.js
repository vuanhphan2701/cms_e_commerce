// productApi.js
import api from "./axios";

export async function getAllProducts({ page = 1, limit = 10, sortBy = "id", order = "desc", include = "" }) {
  const res = await api.get("product", {
    params: { page, limit, sortBy, order, include }
  });

  return {
    items: res.data.data.data,  
    meta: res.data.data.meta,
  };
}

export const getProductById = async (id) => {
  const res = await api.get(`product/${id}`);
  return res.data.data;
};

export const createProduct = async (product) => {
  const res = await api.post("product", product);
  return res.data.data;
};

export const updateProduct = async (id, data) => {
  try {
    const res = await api.put(`product/${id}`, data);
    return res.data.data;
  } catch (error) {
    if (error.response?.status === 409) {
      throw new Error("CONFLICT");
    }
    throw error;
  }
};

export const deleteProduct = async (id) => {
  await api.delete(`product/${id}`);
  return true;
};
