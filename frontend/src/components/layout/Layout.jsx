// components/Layout/MainLayout.jsx
import Sidebar from './SideBar';
import Header from './Header';
import Footer from './Footer';
import { useEffect } from 'react';
import { useNavigate } from 'react-router-dom';

const Layout = ({ children }) => {
  const navigate = useNavigate();

  useEffect(() => {
    // Check token on mount
    const token = localStorage.getItem('token');
    if (!token) {
      navigate('/login');
    }

    // Listen for storage changes from other tabs
    // When tab A logs out (removes token), tab B detects it here
    const handleStorageChange = (e) => {
      if (e.key === 'token' && !e.newValue) {
        navigate('/login');
      }
    };

    window.addEventListener('storage', handleStorageChange);
    return () => window.removeEventListener('storage', handleStorageChange);
  }, [navigate]);

  return (
    <div className="flex bg-gray-50">
      {/* Sidebar - Cố định */}
      <Sidebar />

      {/* Vùng đệm cho Sidebar, để Main Content không bị ẩn */}
      <div className="w-64 flex-shrink-0"></div>

      {/* Main Content */}
      <div className="flex-grow flex flex-col min-h-screen">
        <Header />
        <main className="p-6 flex-grow">
          {children}
        </main>
        <Footer />
      </div>
    </div>
  );
};

export default Layout;