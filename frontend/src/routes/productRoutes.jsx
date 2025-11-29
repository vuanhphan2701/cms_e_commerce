// src/routes/productRoutes.jsx
import { Route } from "react-router-dom";
import Products from "../pages/Products";
import Edit from "../pages/ProductsEdit";
import Create from "../pages/ProductsCreate";
import Detail from "../pages/Detail";
import Dashboard from "../pages/Dashboard";

export const productRoutes = (
  <>
    <Route path="/" element={<Dashboard />} />
    <Route path="/products" element={<Products />} />
    <Route path="/product/edit/:id" element={<Edit />} />
    <Route path="/product/new" element={<Create />} />
    <Route path="/product/:id" element={<Detail />} />
  </>
);
