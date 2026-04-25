import React, { useState, useEffect } from "react";
import Layout from "../../components/layout/Layout";
import ProductForm from "../../components/products/ProductForm";
import { createProduct } from "../../api/productApi";
import { getBrands } from "../../api/brandApi";
import { getCategories } from "../../api/categoryApi";
import { getSuppliers } from "../../api/supplierApi";
import { useNavigate } from "react-router-dom";
import { useAlert } from "../../components/common/AlertContext";

const ProductCreate = () => {

  // use alert context
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const [brands, setBrands] = useState([]);
  const [categories, setCategories] = useState([]);
  const [suppliers, setSuppliers] = useState([]);

  useEffect(() => {
    getBrands().then(res => setBrands(res.data));
    getCategories().then(res => setCategories(res.data));
    getSuppliers().then(res => setSuppliers(res.data));

  }, []);

  const handleCreate = async (data) => {
    try {
      await createProduct(data);
      showAlert("Tạo sản phẩm thành công!");
      navigate("/product");
    } catch (err) {
      // Nếu Laravel trả 422 lỗi validate
      if (err.response && err.response.status === 422) {

        const errorBag = err.response.data.message;
       // console.log(errorBag)

        let messages = "";
        for (let i in errorBag) {
          messages += errorBag[i] + "\n";
        }
       // console.log(messages)

        showAlert("Error:\n" + messages, "error");
        return;
      }

      // Nếu lỗi khác (500, network…)
      console.error(err);
      showAlert("Lỗi khi tạo sản phẩm!");
    }
  };

  return (
    <Layout>
      <div className="max-w-4xl mx-auto bg-white shadow p-6 rounded">
        <h1 className="text-3xl font-bold mb-6">Tạo sản phẩm</h1>

        <ProductForm
          onSubmit={handleCreate}
          brands={brands}
          categories={categories}
          suppliers={suppliers}
        />
      </div>
    </Layout>
  );
};

export default ProductCreate;
