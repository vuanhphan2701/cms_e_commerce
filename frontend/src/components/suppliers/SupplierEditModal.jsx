import React from "react";
import SupplierForm from "./SupplierForm";

export default function SupplierEditModal({
  supplier,
  onClose,
  onSubmit
}) {
  if (!supplier) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg shadow-xl p-6 w-[600px] max-h-[80vh] overflow-y-auto">
        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Chỉnh sửa nhà cung cấp</h2>
          <button onClick={onClose} className="text-red-500 text-lg font-bold">✕</button>
        </div>

        <SupplierForm
          supplier={supplier}
          onSubmit={onSubmit}
        />
      </div>
    </div>
  );
}
