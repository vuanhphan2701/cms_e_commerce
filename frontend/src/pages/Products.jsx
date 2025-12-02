// pages/Products.jsx
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { useProducts } from "../hooks/useProducts";
import { deleteProduct } from "../api/productApi";
import { updateProduct } from "../api/productApi";
import Layout from "../components/layout/Layout";
import ProductTable from "../components/products/ProductTable";
import { useAlert } from "../components/common/AlertContext";


const Products = () => {
  // alert context
  const { showAlert } = useAlert();

  // điều hướng
  const nagative = useNavigate();

  // state để refresh lại trang sau khi xóa hoặc cập nhật
  const [refresh, setRefresh] = useState(0);

  // settate cho modal xem reviews
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [showModal, setShowModal] = useState(false);

  // state cho modal chỉnh sửa sản phẩm
  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  // State điều khiển API query
  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");
  const [include, setInclude] = useState("brands,reviews,suppliers");

  const { products, setProducts, meta, loading } = useProducts({
    page,
    limit,
    sortBy,
    include,
    order,
    refresh
  });

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) return;

    try {
      await deleteProduct(id);
      setProducts(prev => prev.filter(p => p.id !== id));

      // Nếu xóa sản phẩm cuối cùng trên trang, quay về trang trước
      if (products.length === 1 && page > 1) {
        setPage(page - 1);
      }

      showAlert("Xóa sản phẩm thành công!", "success");  // ✅ dùng alert mới
    }
    catch (err) {
      showAlert("Xóa sản phẩm thất bại!", "error");
    }




    // Reload bằng cách set lại page
    //setPage(1);
  };

  if (loading) {
    return (
      <Layout>
        <div className="text-center py-10 text-gray-500">
          Đang tải dữ liệu...
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      {/* Bộ lọc + Tạo sản phẩm */}
      <div className="bg-white p-4 rounded-lg shadow mb-6 flex items-center justify-between">

        {/* LEFT: Bộ lọc */}
        <div className="flex items-center gap-6">

          {/* Limit */}
          <div>
            <label className="text-sm text-gray-600">Số lượng / trang</label>
            <select
              value={limit}
              onChange={(e) => {
                setLimit(Number(e.target.value));
                setPage(1);
              }}
              className="border px-2 py-1 rounded ml-2"
            >
              <option value="5">5</option>
              <option value="10">10</option>
              <option value="20">20</option>
            </select>
          </div>

          {/* Sort */}
          <div>
            <label className="text-sm text-gray-600">Sắp xếp theo</label>
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="border px-2 py-1 rounded ml-2"
            >
              <option value="id">ID</option>
              <option value="price">Giá</option>
              <option value="name">Tên</option>
              <option value="quantity">Tồn kho</option>
            </select>
          </div>
          <div >
            <label className="text-sm text-gray-600">Order</label>
            <select
              value={order}
              onChange={(e) => setOrder(e.target.value)}
              className="border px-2 py-1 rounded ml-2"
            >
              <option value="desc">Newest</option>
              <option value="asc">Oldest</option>
            </select>
          </div>

        </div>

        {/* RIGHT: Nút tạo */}
        <button
          onClick={() => nagative("/product/create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
        >
          + Tạo sản phẩm
        </button>
      </div>


      {/* Bảng sản phẩm */}
      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Tên Sản Phẩm</th>
                <th>Thương Hiệu</th>
                <th>Nhà Cung Cấp</th>
                <th>Giá</th>

                <th>Tồn Kho</th>

                <th>Trạng Thái</th>

                <th>Reviews</th>
                <th>Hành Động</th>
              </tr>
            </thead>

            <tbody className="bg-white divide-y divide-gray-200">
              {products.map((product) => (
                <ProductTable
                  key={product.id}
                  product={product}
                  onDelete={handleDelete}
                  onShowReviews={() => {
                    setSelectedProduct(product);
                    setShowModal(true);
                  }}
                  onEdit={(p) => {
                    setEditForm(p);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>

          {products.length === 0 && (
            <div className="text-center text-gray-500 py-6">
              Không có sản phẩm nào.
            </div>
          )}
        </div>
      </div>

      {/* Pagination */}
      <div className="flex justify-between items-center mt-4">
        <button
          disabled={page === 1}
          onClick={() => setPage(page - 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>

        <span className="text-gray-700">
          Trang {meta?.page} / {meta?.pageCount}
        </span>

        <button
          disabled={page === meta?.pageCount}
          onClick={() => setPage(page + 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>

      {/* MODAL XEM REVIEWS */}
      {showModal && selectedProduct && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-xl p-6 w-[600px] max-h-[80vh] overflow-y-auto">

            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">
                Reviews: {selectedProduct.name}
              </h2>
              <button
                onClick={() => setShowModal(false)}
                className="text-red-500 text-lg font-bold"
              >
                ✕
              </button>
            </div>

            {selectedProduct.reviews?.length > 0 ? (
              selectedProduct.reviews.map((rv) => (
                <div
                  key={rv.id}
                  className="border rounded p-4 mb-3 bg-gray-50 shadow-sm"
                >
                  <div className="flex items-center justify-between mb-2">
                    <div className="text-yellow-500 font-semibold">
                      {"⭐".repeat(rv.rating)}
                    </div>

                    {rv.is_verified ? (
                      <span className="text-green-600 text-xs font-medium">
                        ✔ Đã xác minh
                      </span>
                    ) : (
                      <span className="text-gray-500 text-xs">Không xác minh</span>
                    )}
                  </div>

                  <p className="text-sm text-gray-800 mb-3">{rv.content}</p>

                  <p className="text-xs text-gray-600">👍 {rv.like_count} lượt thích</p>

                  {rv.reply_content && (
                    <div className="mt-3 p-3 bg-white border rounded">
                      <p className="text-xs font-semibold text-blue-600">
                        Phản hồi từ shop:
                      </p>
                      <p className="text-sm text-gray-700">{rv.reply_content}</p>
                    </div>
                  )}

                  <p className="text-xs text-gray-400 mt-2">
                    {new Date(rv.created_at).toLocaleString("vi-VN")}
                  </p>
                </div>
              ))
            ) : (
              <p className="text-gray-500">Chưa có review nào.</p>
            )}
          </div>
        </div>
      )}
      {/* MODAL CHỈNH SỬA SẢN PHẨM */}
      {showEditModal && editForm && (
        <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-xl p-6 w-[600px] max-h-[80vh] overflow-y-auto">

            <div className="flex justify-between items-center mb-4">
              <h2 className="text-xl font-bold">Chỉnh sửa sản phẩm</h2>
              <button
                onClick={() => setShowEditModal(false)}
                className="text-red-500 text-lg font-bold"
              >
                ✕
              </button>
            </div>

            {/* Form */}
            <div className="space-y-3">

              <div>
                <label className="text-sm text-gray-600">Tên sản phẩm</label>
                <input
                  type="text"
                  value={editForm.name}
                  onChange={(e) =>
                    setEditForm({ ...editForm, name: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">SKU</label>
                <input
                  type="text"
                  value={editForm.sku}
                  onChange={(e) =>
                    setEditForm({ ...editForm, sku: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">Giá</label>
                <input
                  type="number"
                  value={editForm.price}
                  onChange={(e) =>
                    setEditForm({ ...editForm, price: Number(e.target.value) })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">Tồn kho</label>
                <input
                  type="number"
                  value={editForm.quantity}
                  onChange={(e) =>
                    setEditForm({ ...editForm, quantity: Number(e.target.value) })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">Summary</label>
                <textarea
                  rows="2"
                  value={editForm.summary}
                  onChange={(e) =>
                    setEditForm({ ...editForm, summary: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                ></textarea>
              </div>

              <div>
                <label className="text-sm text-gray-600">Mô tả</label>
                <textarea
                  rows="3"
                  value={editForm.description}
                  onChange={(e) =>
                    setEditForm({ ...editForm, description: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                ></textarea>
              </div>

              <div>
                <label className="text-sm text-gray-600">Alias</label>
                <input
                  type="text"
                  value={editForm.alias}
                  onChange={(e) =>
                    setEditForm({ ...editForm, alias: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">Image URL</label>
                <input
                  type="text"
                  value={editForm.image}
                  onChange={(e) =>
                    setEditForm({ ...editForm, image: e.target.value })
                  }
                  className="border px-3 py-2 rounded w-full"
                />
              </div>

              <div>
                <label className="text-sm text-gray-600">Trạng thái</label>
                <select
                  value={editForm.status}
                  onChange={(e) =>
                    setEditForm({ ...editForm, status: Number(e.target.value) })
                  }
                  className="border px-3 py-2 rounded w-full"
                >
                  <option value={1}>Còn hàng</option>
                  <option value={0}>Hết hàng</option>
                </select>
              </div>

            </div>

            {/* Nút lưu */}
            <button
              onClick={async () => {
                try {
                  await updateProduct(editForm.id, editForm);
                  setRefresh(prev => prev + 1);
                  setShowEditModal(false);
                  setPage(1);

                  showAlert("Cập nhật sản phẩm thành công!", "success");   // 🔥 ALERT

                } catch (err) {
                  if (err.message === "CONFLICT") {
                    showAlert("❗ Xung đột dữ liệu: sản phẩm đã bị người khác sửa.", "error");
                    return;
                  }

                  showAlert("Cập nhật thất bại!", "error");              // 🔥 ALERT
                }
              }}
              className="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            >
              Lưu thay đổi
            </button>
          </div>
        </div>
      )}

    </Layout>
  );
};

export default Products;
