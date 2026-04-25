import { Route } from "react-router-dom";
import Categories from "../pages/category/Categories";
import CategoryCreate from "../pages/category/CategoryCreate";

export const categoryRoutes = (
  <>
    <Route path="/category" element={<Categories />} />
    <Route path="/category/create" element={<CategoryCreate />} />
  </>
);
