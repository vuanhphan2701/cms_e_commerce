import { UserCircleIcon, BellIcon } from '@heroicons/react/24/outline';

const Header = () => {
  return (
    <header className="flex items-center justify-between p-4 bg-white shadow-md sticky top-0 z-10">
      <h1 className="text-xl font-bold text-gray-800">CMS Dashboard</h1>
      <div className="flex items-center space-x-4">
        <button className="p-2 rounded-full hover:bg-gray-100 transition">
          <BellIcon className="w-6 h-6 text-gray-600" />
        </button>
        <div className="flex items-center space-x-2 cursor-pointer hover:bg-gray-100 p-2 rounded-lg transition">
          <UserCircleIcon className="w-7 h-7 text-blue-600" />
          <span className="text-sm font-medium text-gray-700 hidden sm:block">Admin</span>
        </div>
      </div>
    </header>
  );
};

export default Header;