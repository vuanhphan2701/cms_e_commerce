import React from "react";
import Layout from "../../components/layout/Layout";
import BrandForm from "../../components/brands/BrandForm";
import { createBrand } from "../../api/brandApi";
import { useNavigate } from "react-router-dom";
import { useAlert } from "../../components/common/AlertContext";

const BrandCreate = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const handleCreate = async (data) => {
    try {
      await createBrand(data);
      showAlert("Tạo thương hiệu thành công!");
      navigate("/brand");
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
      showAlert("Lỗi khi tạo thương hiệu!", "error");
    }
  };

  return (
    <Layout>
      <div className="max-w-4xl mx-auto bg-white shadow p-6 rounded">
        <h1 className="text-3xl font-bold mb-6">Tạo thương hiệu</h1>
        <BrandForm onSubmit={handleCreate} />
      </div>
    </Layout>
  );
};

export default BrandCreate;
