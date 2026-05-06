import React from "react";
import CategoryForm from "./CategoryForm";

export default function CategoryEditModal({
  category,
  categories,
  onClose,
  onSubmit
}) {
  if (!category) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg shadow-xl p-6 w-[600px] max-h-[80vh] overflow-y-auto">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Chỉnh sửa danh mục</h2>
          <button onClick={onClose} className="text-red-500 text-lg font-bold">✕</button>
        </div>

        <CategoryForm
          category={category}
          categories={categories}
          onSubmit={onSubmit}
        />
      </div>
    </div>
  );
}
