// pages/ProductEdit.jsx
import React, { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";
import { createProduct } from "../api/productApi";
import Layout from "../components/layout/Layout";
import ProductForm from "../components/products/ProductForm";

const ProductsCreate = () => {

  const navigate = useNavigate();
  const handleCreate = async (formData) => {
    try {
      await createProduct(formData);
      alert("Tạo sản phẩm thành công!");
      navigate("/products");
    }
    catch (error) {
      console.error("Error creating product:", error);
        console.log("Form data sent:", formData);

      alert("Tạo sản phẩm thất bại!");
    }
  };
  return (
    <Layout>
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-3xl font-bold text-gray-800">
            Tao sản phẩm
          </h1>
        </div>
        <ProductForm onSubmit={handleCreate} />
      </div>
    </Layout>
  );
}


export default ProductsCreate;
