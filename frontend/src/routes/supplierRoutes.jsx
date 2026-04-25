import { Route } from "react-router-dom";
import Suppliers from "../pages/supplier/Suppliers";
import SupplierCreate from "../pages/supplier/SupplierCreate";

export const supplierRoutes = (
  <>
    <Route path="/supplier" element={<Suppliers />} />
    <Route path="/supplier/create" element={<SupplierCreate />} />
  </>
);
