
import { useNavigate } from "react-router-dom";
const ProductTable = ({ product, onDelete, onShowReviews, onEdit }) => {
    const navigate = useNavigate();
    return (
        <tr key={product.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {product.id}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {product.sku}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {product.name}
            </td>


            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {product.brand_name}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {product.supplier_name}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-bold">
                {product.price}đ
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {product.quantity}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span
                    className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      ${product.status === "Còn hàng"
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                        }`}
                >
                    {product.status}
                </span>
            </td>

            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                <button
                    onClick={onShowReviews}
                    className="text-blue-600 hover:text-blue-800 px-2 py-1 rounded-md transition duration-150"
                >
                    Xem Đánh Giá
                </button>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <button
                    onClick={
                        () => onEdit(product)}
                    className="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded-md transition duration-150">
                    Sửa
                </button>
                <button
                    onClick={() => onDelete(product.id)}
                    className="text-red-600 hover:text-red-900 px-2 py-1 rounded-md transition duration-150">
                    Xóa
                </button>
            </td>
        </tr >

    )
};
export default ProductTable;