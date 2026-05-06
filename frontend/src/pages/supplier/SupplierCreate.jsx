import React from "react";
import Layout from "../../components/layout/Layout";
import SupplierForm from "../../components/suppliers/SupplierForm";
import { createSupplier } from "../../api/supplierApi";
import { useNavigate } from "react-router-dom";
import { useAlert } from "../../components/common/AlertContext";

const SupplierCreate = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const handleCreate = async (data) => {
    try {
      await createSupplier(data);
      showAlert("Tạo nhà cung cấp thành công!");
      navigate("/supplier");
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
      showAlert("Lỗi khi tạo nhà cung cấp!", "error");
    }
  };

  return (
    <Layout>
      <div className="max-w-4xl mx-auto bg-white shadow p-6 rounded">
        <h1 className="text-3xl font-bold mb-6">Tạo nhà cung cấp</h1>
        <SupplierForm onSubmit={handleCreate} />
      </div>
    </Layout>
  );
};

export default SupplierCreate;
