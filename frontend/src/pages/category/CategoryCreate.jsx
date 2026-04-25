import React from "react";
import Layout from "../../components/layout/Layout";
import CategoryForm from "../../components/categories/CategoryForm";
import { createCategory } from "../../api/categoryApi";
import { useNavigate } from "react-router-dom";
import { useAlert } from "../../components/common/AlertContext";
import { useCategories } from "../../hooks/useCategories";

const CategoryCreate = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const { categories: allCategories } = useCategories();

  const handleCreate = async (data) => {
    try {
      await createCategory(data);
      showAlert("Tạo danh mục thành công!");
      navigate("/category");
    } catch (err) {
      if (err.response && err.response.status === 422) {
        const errorBag = err.response.data.message;
        let messages = "";
        for (let i in errorBag) {
          messages += errorBag[i] + "\n";
        }
        showAlert("Error:\n" + messages, "error");
        return;
      }
      console.error(err);
      showAlert("Lỗi khi tạo danh mục!", "error");
    }
  };

  return (
    <Layout>
      <div className="max-w-4xl mx-auto bg-white shadow p-6 rounded">
        <h1 className="text-3xl font-bold mb-6">Tạo danh mục</h1>
        <CategoryForm onSubmit={handleCreate} categories={allCategories} />
      </div>
    </Layout>
  );
};

export default CategoryCreate;
