// src/routes/productRoutes.jsx
import { Route } from "react-router-dom";
import Products from "../pages/product/Products";
import Create from "../pages/product/ProductsCreate";
import Dashboard from "../pages/Dashboard";

export const productRoutes = (
  <>
    <Route path="/" element={<Dashboard />} />
    <Route path="/product" element={<Products />} />
    <Route path="/product/create" element={<Create />} />
  </>
);
