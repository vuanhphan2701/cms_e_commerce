// components/products/ProductForm.jsx
import React, { useState, useEffect } from "react";

const STATUS_OPTIONS = [
  { value: 1, label: "Đang bán" },
  { value: 2, label: "Bản nháp" },
  { value: 3, label: "Chờ duyệt" },
  { value: 4, label: "Hết hàng" },
  { value: 5, label: "Ẩn" },
  { value: 6, label: "Ngừng bán" },
];

const ProductForm = ({ onSubmit, brands, categories, suppliers, product = {} }) => {
  const [formData, setFormData] = useState({
    name: "",
    sku: "",
    price: 0,
    quantity: 0,
    alias: "",
    image: "",
    summary: "",
    description: "",
    brand_id: "",
    category_id: "",
    supplier_id: "",
    status: 1,
  });

  useEffect(() => {
    if (product && Object.keys(product).length > 0) {
      setFormData(product);
    }
  }, [product]);

  const handleChange = (e) => {
    let value = e.target.value;
    const numericFields = ["price", "quantity", "brand_id", "category_id", "supplier_id", "status"];

    if (numericFields.includes(e.target.name)) {
      value = Number(value);
    }

    setFormData({
      ...formData,
      [e.target.name]: value,
    });
  };

  const submitForm = (e) => {
    e.preventDefault();
    onSubmit(formData);
  };

  return (
    <form onSubmit={submitForm} className="space-y-6">

      {/* NAME + SKU */}
      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="text-sm">Tên sản phẩm</label>
          <input
            type="text"
            name="name"
            className="border rounded px-3 py-2 w-full"
            value={formData.name}
            onChange={handleChange}
          />
        </div>

        <div>
          <label className="text-sm">SKU</label>
          <input
            type="text"
            name="sku"
            className="border rounded px-3 py-2 w-full"
            value={formData.sku}
            onChange={handleChange}
          />
        </div>
      </div>

      {/* PRICE + QUANTITY */}
      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="text-sm">Giá</label>
          <input
            type="number"
            name="price"
            className="border rounded px-3 py-2 w-full"
            value={formData.price}
            onChange={handleChange}
          />
        </div>

        <div>
          <label className="text-sm">Số lượng tồn kho</label>
          <input
            type="number"
            name="quantity"
            className="border rounded px-3 py-2 w-full"
            value={formData.quantity}
            onChange={handleChange}
          />
        </div>
      </div>

      {/* BRAND */}
      <div>
        <label className="text-sm">Thương hiệu</label>
        <select
          name="brand_id"
          className="border rounded px-3 py-2 w-full"
          value={formData.brand_id}
          onChange={handleChange}
        >
          <option value="">-- Chọn thương hiệu --</option>
          {brands?.map((b) => (
            <option key={b.id} value={b.id}>{b.name}</option>
          ))}
        </select>
      </div>

      {/* CATEGORY */}
      <div>
        <label className="text-sm">Danh mục</label>
        <select
          name="category_id"
          className="border rounded px-3 py-2 w-full"
          value={formData.category_id}
          onChange={handleChange}
        >
          <option value="">-- Chọn danh mục --</option>
          {categories?.map((c) => (
            <option key={c.id} value={c.id}>{c.name}</option>
          ))}
        </select>
      </div>

      {/* SUPPLIER */}
      <div>
        <label className="text-sm">Nhà cung cấp</label>
        <select
          name="supplier_id"
          className="border rounded px-3 py-2 w-full"
          value={formData.supplier_id}
          onChange={handleChange}
        >
          <option value="">-- Chọn nhà cung cấp --</option>
          {suppliers?.map((s) => (
            <option key={s.id} value={s.id}>{s.name}</option>
          ))}
        </select>
      </div>

      <div>
        <label className="text-sm">Alias</label>
        <input
          type="text"
          name="alias"
          className="border rounded px-3 py-2 w-full"
          value={formData.alias}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Ảnh đại diện (URL)</label>
        <input
          type="text"
          name="image"
          className="border rounded px-3 py-2 w-full"
          value={formData.image}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Tóm tắt</label>
        <textarea
          name="summary"
          className="border rounded px-3 py-2 w-full"
          rows={2}
          value={formData.summary}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Mô tả</label>
        <textarea
          name="description"
          className="border rounded px-3 py-2 w-full"
          rows={5}
          value={formData.description}
          onChange={handleChange}
        />
      </div>

      {/* STATUS */}
      <div>
        <label className="text-sm">Trạng thái</label>
        <select
          name="status"
          className="border rounded px-3 py-2 w-full"
          value={formData.status}
          onChange={handleChange}
        >
          {STATUS_OPTIONS.map((s) => (
            <option value={s.value}>{s.label}</option>
          ))}
        </select>
      </div>

      <button
        type="submit"
        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Lưu sản phẩm
      </button>
    </form>
  );
};

export default ProductForm;
