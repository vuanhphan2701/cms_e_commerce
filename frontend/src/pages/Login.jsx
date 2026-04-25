import React, { useState } from "react";
import { login } from "../api/authApi";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState("");

  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");
    try {
      const data = await login({ email, password });
      // Lấy token (tuỳ theo json backend trả về, thông thường là data.token hoặc data.access_token)
      const token = data.token || data.access_token; 
      
      if (token) {
        localStorage.setItem("token", token);
        window.location.href = "/dashboard";
      } else {
        setError("Không tìm thấy token xác thực từ server.");
      }
    } catch (err) {
      setError(err.response?.data?.message || "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin.");
    }
  };

  return (
    <div style={{ maxWidth: 400, margin: "100px auto", padding: "20px", border: "1px solid #ddd", borderRadius: "8px" }}>
      <h2 style={{ textAlign: "center", marginBottom: "20px" }}>Đăng nhập CMS</h2>
      <form onSubmit={handleLogin} style={{ display: "flex", flexDirection: "column", gap: "15px" }}>
        <input
          type="email"
          placeholder="Email"
          value={email}
          onChange={(e) => setEmail(e.target.value)}
          required
          style={{ padding: "10px", borderRadius: "4px", border: "1px solid #ccc" }}
        />
        <input
          type="password"
          placeholder="Password"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
          style={{ padding: "10px", borderRadius: "4px", border: "1px solid #ccc" }}
        />
        <button type="submit" style={{ padding: "10px", backgroundColor: "#007bff", color: "white", border: "none", borderRadius: "4px", cursor: "pointer" }}>
          Đăng nhập
        </button>
      </form>
      {error && <p style={{ color: "red", textAlign: "center", marginTop: "15px" }}>{error}</p>}
      <p style={{ textAlign: "center", marginTop: "15px" }}>
        Chưa có tài khoản? <a href="/register">Đăng ký ngay</a>
      </p>
    </div>
  );
}
