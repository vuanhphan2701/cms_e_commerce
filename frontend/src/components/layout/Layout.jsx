// components/Layout/MainLayout.jsx
import Sidebar from './Sidebar';
import Header from './Header';
import Footer from './Footer';

const Layout = ({ children }) => {
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