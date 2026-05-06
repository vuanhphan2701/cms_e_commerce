import { Route } from "react-router-dom";
import Brands from "../pages/brand/Brands";
import BrandCreate from "../pages/brand/BrandCreate";

export const brandRoutes = (
  <>
    <Route path="/brand" element={<Brands />} />
    <Route path="/brand/create" element={<BrandCreate />} />
  </>
);
