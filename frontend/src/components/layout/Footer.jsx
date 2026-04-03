// components/layout/Footer.jsx
import React from "react";

const Footer = () => {
  return (
    <footer className="bg-white border-t text-center py-3 text-gray-500 text-sm">
      © {new Date().getFullYear()} CMS Dashboard. Phan Anh Vu.
    </footer>
  );
};

export default Footer;
