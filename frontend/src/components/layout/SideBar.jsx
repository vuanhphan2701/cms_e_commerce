import React from 'react';
import { HomeIcon, CubeIcon, Cog6ToothIcon, ArrowLeftOnRectangleIcon, PlusCircleIcon, TagIcon, RectangleStackIcon, TruckIcon, StarIcon } from '@heroicons/react/24/outline';
import { useNavigate } from 'react-router-dom';
import { logout } from '../../api/authApi';

const Sidebar = () => {
  const navigate = useNavigate();

  const navItems = [
    { name: 'Tổng quan', icon: HomeIcon, path: '/' },
    { name: 'Sản phẩm', icon: CubeIcon, path: '/product' },
    { name: 'Danh mục', icon: RectangleStackIcon, path: '/category' },
    { name: 'Thương hiệu', icon: TagIcon, path: '/brand' },
    { name: 'Nhà cung cấp', icon: TruckIcon, path: '/supplier' },
    { name: 'Đánh giá', icon: StarIcon, path: '/review' },
    { name: 'Cài đặt', icon: Cog6ToothIcon, path: '/settings' },
  ];

  const handleLogout = async (e) => {
    e.preventDefault();
    try {
      await logout();
    } catch (error) {
      console.error("Logout error", error);
    } finally {
      localStorage.removeItem("token");
      navigate('/login');
    }
  };

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
              href={item.path}
              className={`flex items-center space-x-3 p-3 rounded-lg transition duration-200 
                ${item.path === '/products' ? 'hover:bg-gray-700' : 'hover:bg-gray-700'}`}
            >
              <item.icon className="w-6 h-6" />
              <span className="text-md font-medium">{item.name}</span>
            </a>
          ))}
        </nav>
      </div>

      {/* Logout */}
      <div className="border-t border-gray-700 pt-4">
        <button
          onClick={handleLogout}
          className="w-full flex items-center space-x-3 p-3 rounded-lg hover:bg-red-500 hover:text-white transition duration-200"
        >
          <ArrowLeftOnRectangleIcon className="w-6 h-6" />
          <span className="text-md font-medium">Đăng Xuất</span>
        </button>
      </div>
    </div>
  );
};

export default Sidebar;