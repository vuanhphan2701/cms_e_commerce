import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useSuppliers } from "../../hooks/useSuppliers";
import { deleteSupplier, updateSupplier } from "../../api/supplierApi";
import Layout from "../../components/layout/Layout";
import SupplierTable from "../../components/suppliers/SupplierTable";
import { useAlert } from "../../components/common/AlertContext";
import SupplierEditModal from "../../components/suppliers/SupplierEditModal";

const Suppliers = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");

  const { suppliers, setSuppliers, meta, loading } = useSuppliers({
    page,
    limit,
    sortBy,
    order,
  });

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa nhà cung cấp này?")) return;
    try {
      await deleteSupplier(id);
      setSuppliers((prev) => prev.filter((s) => s.id !== id));
      if (suppliers.length === 1 && page > 1) {
        setPage(page - 1);
      }
      showAlert("Xóa nhà cung cấp thành công!", "success");
    } catch (err) {
      showAlert("Xóa nhà cung cấp thất bại!", "error");
    }
  };

  if (loading) {
    return (
      <Layout>
        <div className="text-center py-10 text-gray-500">Đang tải dữ liệu...</div>
      </Layout>
    );
  }

  return (
    <Layout>
      <div className="bg-white p-4 rounded-lg shadow mb-6 flex items-center justify-between">
        <h1 className="text-2xl font-bold">Quản lý nhà cung cấp</h1>
        <button
          onClick={() => navigate("/supplier/create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
        >
          + Tạo nhà cung cấp
        </button>
      </div>

      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên Nhà Cung Cấp</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200 text-center">
              {suppliers.map((supplier) => (
                <SupplierTable
                  key={supplier.id}
                  supplier={supplier}
                  onDelete={handleDelete}
                  onEdit={(s) => {
                    setEditForm(s);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>
          {suppliers.length === 0 && (
            <div className="text-center text-gray-500 py-6">Không có nhà cung cấp nào.</div>
          )}
        </div>
      </div>

      <div className="flex justify-between items-center mt-4">
        <button
          disabled={page === 1}
          onClick={() => setPage(page - 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>
        <span className="text-gray-700">
          Trang {meta?.current_page || page} / {meta?.last_page || 1}
        </span>
        <button
          disabled={!meta?.last_page || page === meta?.last_page}
          onClick={() => setPage(page + 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>

      {showEditModal && editForm && (
        <SupplierEditModal
          supplier={editForm}
          onClose={() => setShowEditModal(false)}
          onSubmit={async (data) => {
            try {
              await updateSupplier(editForm.id, data);
              const updatedSupplier = { ...editForm, ...data };
              setSuppliers((prev) =>
                prev.map((s) => (s.id === editForm.id ? updatedSupplier : s))
              );
              setEditForm(updatedSupplier);
              showAlert("Cập nhật thành công!", "success");
              setShowEditModal(false);
            } catch (err) {
              showAlert("Cập nhật thất bại!", "error");
            }
          }}
        />
      )}
    </Layout>
  );
};

export default Suppliers;
