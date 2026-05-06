import api from "./axios";

export async function getAllReviews(options = {}) {
  const res = await api.get("review", { params: options });
  return {
    items: res.data.data.data,
    meta: res.data.data.meta || res.data.data,
  };
}

export async function getReviews() {
  const res = await api.get("review");
  return res.data.data;
}

export async function getReviewById(id) {
  const res = await api.get(`review/${id}`);
  return res.data.data;
}

export async function createReview(data) {
  const res = await api.post("review", data);
  return res.data.data;
}

export async function updateReview(id, data) {
  const res = await api.put(`review/${id}`, data);
  return res.data.data;
}

export async function deleteReview(id) {
  await api.delete(`review/${id}`);
  return true;
}
