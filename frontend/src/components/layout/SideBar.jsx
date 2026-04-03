// components/Layout/Sidebar.jsx
import React from 'react';
import { HomeIcon, CubeIcon, Cog6ToothIcon, ArrowLeftOnRectangleIcon, PlusCircleIcon } from '@heroicons/react/24/outline';
// import { NavLink } from 'react-router-dom'; // Giả sử dùng NavLink

const Sidebar = () => {
  const navItems = [
    { name: 'Tổng quan', icon: HomeIcon, path: '/' },
    { name: 'Sản phẩm', icon: CubeIcon, path: '/product' },
    { name: 'Tạo', icon: PlusCircleIcon, path: '/product/create/' },
    { name: 'Cài đặt', icon: Cog6ToothIcon, path: '/settings' },
  ];

  return (
    <div className="w-64 bg-gray-800 text-white min-h-screen p-4 flex flex-col justify-between fixed h-full">
      {/* Logo/Tên dự án */}
      <div>
        <div className="text-2xl font-extrabold mb-8 text-blue-400">
          E-commerce CMS
        </div>

        {/* Menu chính */}
        <nav className="space-y-2">
          {navItems.map((item) => (
            <a
              key={item.name}
              href={item.path} // Thay bằng <NavLink to={item.path}>
              className={`flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                ${item.path === '/products' ? 'hover:bg-gray-700' : 'hover:bg-gray-700'}`} // active-link là style custom trong index.css
            >
              <item.icon className="w-6 h-6" />
              <span className="text-md font-medium">{item.name}</span>
            </a>
          ))}
        </nav>
      </div>

      {/* Logout */}
      <div className="border-t border-gray-700 pt-4">
        <a
          href="#"
          className="flex items-center space-x-3 p-3 rounded-lg hover:bg-red-500 hover:text-white transition duration-200"
        >
          <ArrowLeftOnRectangleIcon className="w-6 h-6" />
          <span className="text-md font-medium">Đăng Xuất</span>
        </a>
      </div>
    </div>
  );
};

export default Sidebar;