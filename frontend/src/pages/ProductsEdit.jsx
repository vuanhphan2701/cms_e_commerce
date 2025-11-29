// pages/ProductEdit.jsx
import React, { useEffect, useState } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { getProductById, updateProduct } from "../api/productApi";
import Layout from "../components/layout/Layout";
import ProductForm from "../components/products/ProductForm";

const ProductEdit = () => {
  const { id } = useParams();
  const navigate = useNavigate();

  const [product, setProduct] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    getProductById(id)
      .then((data) => setProduct(data))
      .catch((err) => console.error("Error loading product:", err))
      .finally(() => setLoading(false));
  }, [id]);

  const handleUpdate = async (formData) => {
    try {
      await updateProduct(id, formData);
      alert("Cập nhật sản phẩm thành công!");
      navigate("/products"); // quay về danh sách
    } catch (error) {
      console.error("Error updating product:", error);
      alert("Cập nhật thất bại!");
    }
  };

  if (loading)
    return (
      <Layout>
        <div className="text-center text-gray-500 py-10">
          Đang tải dữ liệu sản phẩm...
        </div>
      </Layout>
    );

  if (!product)
    return (
      <Layout>
        <div className="text-center text-red-500 py-10">
          Không tìm thấy sản phẩm.
        </div>
      </Layout>
    );

  return (
    <Layout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold text-gray-800">
            Chỉnh sửa sản phẩm
          </h1>
          <button
            onClick={() => navigate(-1)}
            className="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded-md shadow-sm"
          >
            Quay lại
          </button>
        </div>

        <ProductForm product={product} onSubmit={handleUpdate} />
      </div>
    </Layout>
  );
};

export default ProductEdit;
