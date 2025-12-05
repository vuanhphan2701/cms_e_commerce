import React from "react";

export default function ProductReviewsModal({ product, onClose }) {
  if (!product) return null;

  return (
    <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">

      <div className="bg-white rounded-lg shadow-xl p-6 w-[600px] max-h-[80vh] overflow-y-auto">

        <div className="flex justify-between items-center mb-4">
          <h2 className="text-xl font-bold">Reviews: {product.name}</h2>
          <button onClick={onClose} className="text-red-500 text-lg font-bold">
            ✕
          </button>
        </div>

        {product.reviews?.length > 0 ? (
          product.reviews.map(rv => (
            <div key={rv.id} className="border rounded p-4 mb-3 bg-gray-50 shadow-sm">

              <div className="flex items-center justify-between mb-2">
                <div className="text-yellow-500 font-semibold">
                  {"⭐".repeat(rv.rating)}
                </div>

                {rv.is_verified ? (
                  <span className="text-green-600 text-xs font-medium">✔ Verified</span>
                ) : (
                  <span className="text-gray-500 text-xs">Not verified</span>
                )}
              </div>

              <p className="text-sm text-gray-800 mb-3">{rv.content}</p>

              <p className="text-xs text-gray-600">👍 {rv.like_count} likes</p>

              {rv.reply_content && (
                <div className="mt-3 p-3 bg-white border rounded">
                  <p className="text-xs font-semibold text-blue-600">Shop reply:</p>
                  <p className="text-sm text-gray-700">{rv.reply_content}</p>
                </div>
              )}

              <p className="text-xs text-gray-400 mt-2">
                {new Date(rv.created_at).toLocaleString("vi-VN")}
              </p>
            </div>
          ))
        ) : (
          <p className="text-gray-500 text-center">No reviews yet.</p>
        )}
      </div>
    </div>
  );
}
