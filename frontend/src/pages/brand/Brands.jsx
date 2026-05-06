import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useBrands } from "../../hooks/useBrands";
import { deleteBrand, updateBrand } from "../../api/brandApi";
import Layout from "../../components/layout/Layout";
import BrandTable from "../../components/brands/BrandTable";
import { useAlert } from "../../components/common/AlertContext";
import BrandEditModal from "../../components/brands/BrandEditModal";

const Brands = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");

  const { brands, setBrands, meta, loading } = useBrands({
    page,
    limit,
    sortBy,
    order,
  });

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa thương hiệu này?")) return;
    try {
      await deleteBrand(id);
      setBrands((prev) => prev.filter((b) => b.id !== id));
      if (brands.length === 1 && page > 1) {
        setPage(page - 1);
      }
      showAlert("Xóa thương hiệu thành công!", "success");
    } catch (err) {
      showAlert("Xóa thương hiệu thất bại!", "error");
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
        <h1 className="text-2xl font-bold">Quản lý thương hiệu</h1>
        <button
          onClick={() => navigate("/brand/create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
        >
          + Tạo thương hiệu
        </button>
      </div>

      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên Thương Hiệu</th>
                <th>Alias</th>
                <th>Tóm tắt</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200 text-center">
              {brands.map((brand) => (
                <BrandTable
                  key={brand.id}
                  brand={brand}
                  onDelete={handleDelete}
                  onEdit={(b) => {
                    setEditForm(b);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>
          {brands.length === 0 && (
            <div className="text-center text-gray-500 py-6">Không có thương hiệu nào.</div>
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
        <BrandEditModal
          brand={editForm}
          onClose={() => setShowEditModal(false)}
          onSubmit={async (data) => {
            try {
              await updateBrand(editForm.id, data);
              const updatedBrand = { ...editForm, ...data };
              setBrands((prev) =>
                prev.map((b) => (b.id === editForm.id ? updatedBrand : b))
              );
              setEditForm(updatedBrand);
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

export default Brands;
