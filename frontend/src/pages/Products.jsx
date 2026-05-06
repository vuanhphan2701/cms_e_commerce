// pages/Products.jsx
import { useState, useEffect } from "react";
// import navigation hook
import { useNavigate } from "react-router-dom";
// import custom hook to load products
import { useProducts } from "../hooks/useProducts";
import { deleteProduct } from "../api/productApi";
import { updateProduct } from "../api/productApi";
// import Layout component
import Layout from "../components/layout/Layout";
// import ProductTable component
import ProductTable from "../components/products/ProductTable";
// import ProductForm component
import ProductForm from "../components/products/ProductForm";
// import alert context
import { useAlert } from "../components/common/AlertContext";
// import APIs to load brands, categories, suppliers
import { getBrands } from "../api/brandApi";
import { getCategories } from "../api/categoryApi";
import { getSuppliers } from "../api/supplierApi";
// import ProductFilters component
import ProductFilters from "../components/products/ProductFilter";
// import ProductEditModal component
import ProductEditModal from "../components/products/ProductEditModal";
// import ProductReviewModal component
import ProductReviewsModal from "../components/products/ProductReviewsModal";

const Products = () => {
  // alert context
  const { showAlert } = useAlert();

  // điều hướng
  const nagative = useNavigate();

  // load brands, category, supplier
  const [brands, setBrands] = useState([]);
  const [categories, setCategories] = useState([]);
  const [suppliers, setSuppliers] = useState([]);

  // load brands, categories, suppliers on component mount
  useEffect(() => {
    getBrands().then(res => setBrands(res.data));
    getCategories().then(res => setCategories(res.data));
    getSuppliers().then(res => setSuppliers(res.data));

  }, []);


  // settate for reviews modal
  const [selectedProduct, setSelectedProduct] = useState(null);
  const [showModal, setShowModal] = useState(false);

  // state for edit product modal
  const [showEditModal, setShowEditModal] = useState(false);
  const [editForm, setEditForm] = useState({});

  // state for filters
  const [page, setPage] = useState(1);
  const [limit, setLimit] = useState(10);
  const [sortBy, setSortBy] = useState("id");
  const [order, setOrder] = useState("desc");

  // filter for category, brand, supplier
  const [filterCategory, setFilterCategory] = useState("");
  const [filterBrand, setFilterBrand] = useState("");
  const [filterSupplier, setFilterSupplier] = useState("");

  // load all related entities
  const include = "brands,reviews,suppliers";

  const { products, setProducts, meta, loading } = useProducts({
    page,
    limit,
    sortBy,
    include,
    order,
    category_id: filterCategory,
    brand_id: filterBrand,
    supplier_id: filterSupplier
  });

  // handle delete product
  const handleDelete = async (id) => {
    if (!window.confirm("Bạn có chắc chắn muốn xóa sản phẩm này?")) return;

    try {
      await deleteProduct(id);
      setProducts(prev => prev.filter(p => p.id !== id));

      // Nếu xóa sản phẩm cuối cùng trên trang, quay về trang trước
      if (products.length === 1 && page > 1) {
        setPage(page - 1);
      }

      showAlert("Xóa sản phẩm thành công!", "success");  // ✅ dùng alert mới
    }
    catch (err) {
      showAlert("Xóa sản phẩm thất bại!", "error");
    }



  };

  if (loading) {
    return (
      <Layout>
        <div className="text-center py-10 text-gray-500">
          Đang tải dữ liệu...
        </div>
      </Layout>
    );
  }

  return (
    <Layout>
      {/* Bộ lọc + Tạo sản phẩm */}
      <div className="bg-white p-4 rounded-lg shadow mb-6 flex items-center justify-between">

        {/* LEFT: Bộ lọc */}
        <ProductFilters
          limit={limit}
          setLimit={setLimit}
          sortBy={sortBy}
          setSortBy={setSortBy}
          order={order}
          setOrder={setOrder}

          filterCategory={filterCategory}
          setFilterCategory={setFilterCategory}
          filterBrand={filterBrand}
          setFilterBrand={setFilterBrand}
          filterSupplier={filterSupplier}
          setFilterSupplier={setFilterSupplier}

          categories={categories}
          brands={brands}
          suppliers={suppliers}
        />


        {/* RIGHT: Nút tạo */}
        <button
          onClick={() => nagative("/product/create")}
          className="bg-blue-600 text-white px-4 py-2 rounded-md shadow hover:bg-blue-700 transition"
        >
          + Tạo sản phẩm
        </button>
      </div>


      {/* Bảng sản phẩm */}
      <div className="bg-white p-6 rounded-lg shadow-xl">
        <div className="overflow-x-auto">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th>ID</th>
                <th>SKU</th>
                <th>Tên Sản Phẩm</th>
                <th>Thương Hiệu</th>
                <th>Nhà Cung Cấp</th>
                <th>Giá</th>

                <th>Tồn Kho</th>

                <th>Trạng Thái</th>

                <th>Reviews</th>
                <th>Hành Động</th>
              </tr>
            </thead>

            <tbody className="bg-white divide-y divide-gray-200">
              {products.map((product) => (
                <ProductTable
                  key={product.id}
                  product={product}
                  onDelete={handleDelete}
                  onShowReviews={() => {
                    setSelectedProduct(product);
                    setShowModal(true);
                  }}
                  onEdit={(p) => {
                    setEditForm(p);
                    setShowEditModal(true);
                  }}
                />
              ))}
            </tbody>
          </table>

          {products.length === 0 && (
            <div className="text-center text-gray-500 py-6">
              Không có sản phẩm nào.
            </div>
          )}
        </div>
      </div>

      {/* Pagination */}
      <div className="flex justify-between items-center mt-4">
        <button
          disabled={page === 1}
          onClick={() => setPage(page - 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Previous
        </button>

        <span className="text-gray-700">
          Trang {meta?.page} / {meta?.pageCount}
        </span>

        <button
          disabled={page === meta?.pageCount}
          onClick={() => setPage(page + 1)}
          className="px-3 py-1 bg-gray-200 rounded disabled:opacity-50"
        >
          Next
        </button>
      </div>

      {/* MODAL XEM REVIEWS */}
      {showModal && selectedProduct && (
        <ProductReviewsModal
          product={selectedProduct}
          onClose={() => setShowModal(false)}
        />
      )}

      {/* MODAL CHỈNH SỬA SẢN PHẨM */}
      {showEditModal && editForm && (
        <ProductEditModal
          product={editForm}
          brands={brands}
          categories={categories}
          suppliers={suppliers}
          onClose={() => setShowEditModal(false)}
          onSubmit={async (data) => {

            try {
              await updateProduct(editForm.id, data);

              const updatedProduct = {
                ...editForm,
                ...data,
                brand: brands.find(b => b.id === data.brand_id),
                supplier: suppliers.find(s => s.id === data.supplier_id),
                category: categories.find(c => c.id === data.category_id),
                version: editForm.version + 1
              };

              setProducts(prev =>
                prev.map(p => p.id === editForm.id ? updatedProduct : p)
              );

              setEditForm(updatedProduct);
              setShowEditModal(false);
              showAlert("Product updated successfully!", "success");

            } catch (err) {
              if (err.message === "CONFLICT") {
                showAlert("❗ Data conflict: someone updated this product.", "error");
                return;
              }

              showAlert("Update failed!", "error");
            }
          }}
        />)}


    </Layout>
  );
};

export default Products;
