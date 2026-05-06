import React, { useState, useEffect } from "react";

const STATUS_OPTIONS = [
  { value: 1, label: "Hiện" },
  { value: 0, label: "Ẩn" },
];

const ReviewForm = ({ onSubmit, review = {} }) => {
  const [formData, setFormData] = useState({
    rating: 5,
    content: "",
    reply_content: "",
    status: 1,
    is_verified: 0,
  });

  useEffect(() => {
    if (review && Object.keys(review).length > 0) {
      setFormData(review);
    }
  }, [review]);

  const handleChange = (e) => {
    let value = e.target.value;
    if (e.target.name === "status" || e.target.name === "is_verified" || e.target.name === "rating") {
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
      <div className="grid grid-cols-2 gap-4">
        <div>
          <label className="text-sm">Đánh giá (1-5)</label>
          <input
            type="number"
            min="1" max="5"
            name="rating"
            className="border rounded px-3 py-2 w-full bg-gray-100"
            value={formData.rating || 5}
            disabled
          />
        </div>
        <div>
          <label className="text-sm">Trạng thái xác minh mua hàng</label>
          <select
            name="is_verified"
            className="border rounded px-3 py-2 w-full bg-gray-100"
            value={formData.is_verified}
            disabled
          >
            <option value={1}>Đã mua</option>
            <option value={0}>Chưa xác minh</option>
          </select>
        </div>
      </div>

      <div>
        <label className="text-sm">Nội dung đánh giá</label>
        <textarea
          name="content"
          className="border rounded px-3 py-2 w-full bg-gray-100"
          rows={3}
          value={formData.content || ""}
          disabled
        />
      </div>

      <div>
        <label className="text-sm">Nội dung phản hồi (Admin)</label>
        <textarea
          name="reply_content"
          className="border rounded px-3 py-2 w-full"
          rows={3}
          value={formData.reply_content || ""}
          onChange={handleChange}
        />
      </div>

      <div>
        <label className="text-sm">Trạng thái hiển thị</label>
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
        Lưu đánh giá / Phản hồi
      </button>
    </form>
  );
};

export default ReviewForm;
