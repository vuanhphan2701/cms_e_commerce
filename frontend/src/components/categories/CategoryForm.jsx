import React, { useState, useEffect } from "react";

const STATUS_OPTIONS = [
  { value: 1, label: "Hoạt động" },
  { value: 0, label: "Ngừng hoạt động" },
];

const CategoryForm = ({ onSubmit, categories, category = {} }) => {
  const [formData, setFormData] = useState({
    name: "",
    alias: "",
    image: "",
    summary: "",
    description: "",
    parent_id: "",
    status: 1,
  });

  useEffect(() => {
    if (category && Object.keys(category).length > 0) {
      setFormData(category);
    }
  }, [category]);

  const handleChange = (e) => {
    let value = e.target.value;
    if (e.target.name === "status" || e.target.name === "parent_id") {
      value = value ? Number(value) : "";
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
      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="text-sm">Tên danh mục</label>
          <input
            type="text"
            name="name"
            className="border rounded px-3 py-2 w-full"
            value={formData.name || ""}
            onChange={handleChange}
          />
        </div>
        <div>
          <label className="text-sm">Alias</label>
          <input
            type="text"
            name="alias"
            className="border rounded px-3 py-2 w-full"
            value={formData.alias || ""}
            onChange={handleChange}
          />
        </div>
      </div>

      <div>
        <label className="text-sm">Danh mục cha</label>
        <select
          name="parent_id"
          className="border rounded px-3 py-2 w-full"
          value={formData.parent_id || ""}
          onChange={handleChange}
        >
          <option value="">-- Không có --</option>
          {categories?.filter(c => c.id !== category.id).map((c) => (
            <option key={c.id} value={c.id}>{c.name}</option>
          ))}
        </select>
      </div>

      <div>
        <label className="text-sm">Ảnh đại diện (URL)</label>
        <input
          type="text"
          name="image"
          className="border rounded px-3 py-2 w-full"
          value={formData.image || ""}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Tóm tắt</label>
        <textarea
          name="summary"
          className="border rounded px-3 py-2 w-full"
          rows={2}
          value={formData.summary || ""}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Mô tả chi tiết</label>
        <textarea
          name="description"
          className="border rounded px-3 py-2 w-full"
          rows={5}
          value={formData.description || ""}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Trạng thái</label>
        <select
          name="status"
          className="border rounded px-3 py-2 w-full"
          value={formData.status}
          onChange={handleChange}
        >
          {STATUS_OPTIONS.map((s) => (
            <option key={s.value} value={s.value}>{s.label}</option>
          ))}
        </select>
      </div>

      <button
        type="submit"
        className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
      >
        Lưu danh mục
      </button>
    </form>
  );
};

export default CategoryForm;
