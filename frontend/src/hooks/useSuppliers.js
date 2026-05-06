import { useEffect, useState } from "react";
import { getAllSuppliers } from "../api/supplierApi";

export function useSuppliers(options) {
  const [suppliers, setSuppliers] = useState([]);
  const [meta, setMeta] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    setLoading(true);

    getAllSuppliers(options)
      .then((res) => {
        setSuppliers(res.items);
        setMeta(res.meta?.pagination || res.meta);
      })
      .finally(() => setLoading(false));
  }, [options.page, options.limit, options.sortBy, options.order, options.keyword, options.status]);

  return { suppliers, setSuppliers, meta, loading };
}
