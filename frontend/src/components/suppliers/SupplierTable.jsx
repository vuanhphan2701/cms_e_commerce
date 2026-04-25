import React from "react";

const SupplierTable = ({ supplier, onDelete, onEdit }) => {
    return (
        <tr key={supplier.id} className="hover:bg-gray-50">
            <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {supplier.id}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {supplier.image && <img src={supplier.image} alt={supplier.name} className="h-10 w-10 rounded-full object-cover" />}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {supplier.name}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {supplier.email}
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                {supplier.phone}
            </td>
            <td className="px-6 py-4 whitespace-nowrap">
                <span
                    className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                      ${supplier.status === 1
                            ? "bg-green-100 text-green-800"
                            : "bg-red-100 text-red-800"
                        }`}
                >
                    {supplier.status === 1 ? "Hoạt động" : "Ngừng hoạt động"}
                </span>
            </td>
            <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <button
                    onClick={() => onEdit(supplier)}
                    className="text-indigo-600 hover:text-indigo-900 px-2 py-1 rounded-md transition duration-150">
                    Sửa
                </button>
                <button
                    onClick={() => onDelete(supplier.id)}
                    className="text-red-600 hover:text-red-900 px-2 py-1 rounded-md transition duration-150">
                    Xóa
                </button>
            </td>
        </tr>
    );
};

export default SupplierTable;
