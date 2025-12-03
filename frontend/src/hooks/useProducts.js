import { useEffect, useState } from "react";
import { getAllProducts } from "../api/productApi";

export function useProducts(options) {
  const [products, setProducts] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);

    getAllProducts(options)
      .then((res) => {
        setProducts(res.items);
        setMeta(res.meta.pagination);
      })
      .finally(() => setLoading(false));
  }, [options.page, options.limit, options.sortBy, options.order, options.include]);

  return { products, setProducts, meta, loading };
}
