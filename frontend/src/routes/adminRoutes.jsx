import { Route } from "react-router-dom";
import AdminLogin from "../pages/admin/AdminLogin";
import UserManagement from "../pages/admin/UserManagement";
import UserDetail from "../pages/admin/UserDetail";
import AdminLayout from "../components/layout/AdminLayout";

export const adminRoutes = (
  <>
    <Route path="/admin/login" element={<AdminLogin />} />
    
    <Route element={<AdminLayout />}>
      <Route path="/admin/dashboard" element={<div className="p-8 text-white text-center text-3xl font-bold">Admin Dashboard (In Progress)</div>} />
      <Route path="/admin/users" element={<UserManagement />} />
      <Route path="/admin/users/:id" element={<UserDetail />} />
    </Route>
  </>
);
