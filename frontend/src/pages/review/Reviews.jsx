import { useState } from "react";
import { useReviews } from "../../hooks/useReviews";
import { deleteReview, updateReview } from "../../api/reviewApi";
import Layout from "../../components/layout/Layout";
import ReviewTable from "../../components/reviews/ReviewTable";
import { useAlert } from "../../components/common/AlertContext";
import ReviewEditModal from "../../components/reviews/ReviewEditModal";

const Reviews = () => {
  const { showAlert } = useAlert();

  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");

  const { reviews, setReviews, meta, loading } = useReviews({
    page,
    limit,
    sortBy,
    order,
  });

  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa đánh giá này?")) return;
    try {
      await deleteReview(id);
      setReviews((prev) => prev.filter((r) => r.id !== id));
      if (reviews.length === 1 && page > 1) {
        setPage(page - 1);
      }
      showAlert("Xóa đánh giá thành công!", "success");
    } catch (err) {
      showAlert("Xóa đánh giá thất bại!", "error");
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
        <h1 className="text-2xl font-bold">Quản lý đánh giá</h1>
      </div>

      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>Sản phẩm ID</th>
                <th>User ID</th>
                <th>Rating</th>
                <th>Nội dung</th>
                <th>Xác minh</th>
                <th>Trạng Thái</th>
                <th>Hành Động</th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200 text-center">
              {reviews.map((review) => (
                <ReviewTable
                  key={review.id}
                  review={review}
                  onDelete={handleDelete}
                  onEdit={(r) => {
                    setEditForm(r);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>
          {reviews.length === 0 && (
            <div className="text-center text-gray-500 py-6">Không có đánh giá nào.</div>
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
        <ReviewEditModal
          review={editForm}
          onClose={() => setShowEditModal(false)}
          onSubmit={async (data) => {
            try {
              await updateReview(editForm.id, data);
              const updatedReview = { ...editForm, ...data };
              setReviews((prev) =>
                prev.map((r) => (r.id === editForm.id ? updatedReview : r))
              );
              setEditForm(updatedReview);
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

export default Reviews;
