import React from "react";

const ProductFilters = ({
  limit,
  setLimit,
  sortBy,
  setSortBy,
  order,
  setOrder,
  filterCategory,
  setFilterCategory,
  filterBrand,
  setFilterBrand,
  filterSupplier,
  setFilterSupplier,
  categories,
  brands,
  suppliers
}) => {
      console.log("%c ProductFilters rendered", "color: green;");

  return (
    <div className="flex items-center gap-6">

      {/* Limit */}
      <div>
        <label className="text-sm text-gray-600">Số lượng / trang</label>
        <select
          value={limit}
          onChange={(e) => {
            setLimit(Number(e.target.value));
          }}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="5">5</option>
          <option value="10">10</option>
          <option value="20">20</option>
        </select>
      </div>

      {/* Sort */}
      <div>
        <label className="text-sm text-gray-600">Sắp xếp theo</label>
        <select
          value={sortBy}
          onChange={(e) => setSortBy(e.target.value)}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="id">ID</option>
          <option value="price">Giá</option>
          <option value="name">Tên</option>
          <option value="quantity">Tồn kho</option>
        </select>
      </div>

      {/* Order */}
      <div>
        <label className="text-sm text-gray-600">Order</label>
        <select
          value={order}
          onChange={(e) => setOrder(e.target.value)}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="desc">Newest</option>
          <option value="asc">Oldest</option>
        </select>
      </div>

      {/* Category Filter */}
      <div>
        <label className="text-sm text-gray-600">Category</label>
        <select
          value={filterCategory}
          onChange={(e) => setFilterCategory(e.target.value)}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="">All</option>
          {categories.map((c) => (
            <option key={c.id} value={c.id}>
              {c.name}
            </option>
          ))}
        </select>
      </div>

      {/* Brand Filter */}
      <div>
        <label className="text-sm text-gray-600">Brand</label>
        <select
          value={filterBrand}
          onChange={(e) => setFilterBrand(e.target.value)}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="">All</option>
          {brands.map((b) => (
            <option key={b.id} value={b.id}>
              {b.name}
            </option>
          ))}
        </select>
      </div>

      {/* Supplier Filter */}
      <div>
        <label className="text-sm text-gray-600">Supplier</label>
        <select
          value={filterSupplier}
          onChange={(e) => setFilterSupplier(e.target.value)}
          className="border px-2 py-1 rounded ml-2"
        >
          <option value="">All</option>
          {suppliers.map((s) => (
            <option key={s.id} value={s.id}>
              {s.name}
            </option>
          ))}
        </select>
      </div>

    </div>
  );
};

export default React.memo(ProductFilters);
