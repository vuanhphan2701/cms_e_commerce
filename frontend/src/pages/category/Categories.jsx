import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useCategoriesPaginated } from "../../hooks/useCategoriesPaginated";
import { deleteCategory, updateCategory } from "../../api/categoryApi";
import Layout from "../../components/layout/Layout";
import CategoryTable from "../../components/categories/CategoryTable";
import { useAlert } from "../../components/common/AlertContext";
import CategoryEditModal from "../../components/categories/CategoryEditModal";
import { useCategories } from "../../hooks/useCategories";

const Categories = () => {
  const { showAlert } = useAlert();
  const navigate = useNavigate();

  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");

  const { categories, setCategories, meta, loading } = useCategoriesPaginated({
    page,
    limit,
    sortBy,
    order,
  });

  const { categories: allCategories } = useCategories(); // For parent dropdown

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa danh mục này?")) return;
    try {
      await deleteCategory(id);
      setCategories((prev) => prev.filter((c) => c.id !== id));
      if (categories.length === 1 && page > 1) {
        setPage(page - 1);
      }
      showAlert("Xóa danh mục thành công!", "success");
    } catch (err) {
      showAlert("Xóa danh mục thất bại!", "error");
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
        <h1 className="text-2xl font-bold">Quản lý danh mục</h1>
        <button
          onClick={() => navigate("/category/create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
        >
          + Tạo danh mục
        </button>
      </div>

      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên Danh Mục</th>
                <th>Alias</th>
                <th>Cha</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200 text-center">
              {categories.map((category) => (
                <CategoryTable
                  key={category.id}
                  category={category}
                  onDelete={handleDelete}
                  onEdit={(c) => {
                    setEditForm(c);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>
          {categories.length === 0 && (
            <div className="text-center text-gray-500 py-6">Không có danh mục nào.</div>
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
        <CategoryEditModal
          category={editForm}
          categories={allCategories}
          onClose={() => setShowEditModal(false)}
          onSubmit={async (data) => {
            try {
              await updateCategory(editForm.id, data);
              const updatedCategory = { ...editForm, ...data };
              setCategories((prev) =>
                prev.map((c) => (c.id === editForm.id ? updatedCategory : c))
              );
              setEditForm(updatedCategory);
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

export default Categories;
