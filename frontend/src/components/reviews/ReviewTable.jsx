import React from "react";

const ReviewTable = ({ review, onDelete, onEdit }) => {
    return (
        <tr key={review.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {review.id}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {review.product_id}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {review.user_id}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {review.rating} ⭐
            </td>
            <td className="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">
                {review.content}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span
                    className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      ${review.is_verified === 1
                            ? "bg-blue-100 text-blue-800"
                            : "bg-gray-100 text-gray-800"
                        }`}
                >
                    {review.is_verified === 1 ? "Đã mua" : "Chưa xác minh"}
                </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span
                    className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      ${review.status === 1
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                        }`}
                >
                    {review.status === 1 ? "Hiện" : "Ẩn"}
                </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <button
                    onClick={() => onEdit(review)}
                    className="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded-md transition duration-150">
                    Phản hồi / Sửa
                </button>
                <button
                    onClick={() => onDelete(review.id)}
                    className="text-red-600 hover:text-red-900 px-2 py-1 rounded-md transition duration-150">
                    Xóa
                </button>
            </td>
        </tr>
    );
};

export default ReviewTable;
