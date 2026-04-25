import React, { useState } from "react";
import { register } from "../api/authApi";

export default function Register() {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [error, setError] = useState("");

  const handleRegister = async (e) => {
    e.preventDefault();
    setError("");

    if (password !== passwordConfirmation) {
      setError("Mật khẩu xác nhận không khớp!");
      return;
    }

    try {
      const data = await register({ 
        name, 
        email, 
        password, 
        password_confirmation: passwordConfirmation 
      });

      const token = data.token || data.access_token;
      if (token) {
        localStorage.setItem("token", token);
        window.location.href = "/dashboard"; // Đăng ký xong tự động đăng nhập và vào dashboard
      } else {
        // Nếu API backend không trả về token luôn, điều hướng qua login
        window.location.href = "/login";
      }
    } catch (err) {
      setError(err.response?.data?.message || "Đăng ký thất bại. Vui lòng thử lại.");
    }
  };

  return (
    <div style={{ maxWidth: 400, margin: "100px auto", padding: "20px", border: "1px solid #ddd", borderRadius: "8px" }}>
      <h2 style={{ textAlign: "center", marginBottom: "20px" }}>Đăng ký CMS</h2>
      <form onSubmit={handleRegister} style={{ display: "flex", flexDirection: "column", gap: "15px" }}>
        <input
          type="text"
          placeholder="Họ và tên"
          value={name}
          onChange={(e) => setName(e.target.value)}
          required
          style={{ padding: "10px", borderRadius: "4px", border: "1px solid #ccc" }}
        />
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
          placeholder="Mật khẩu"
          value={password}
          onChange={(e) => setPassword(e.target.value)}
          required
          style={{ padding: "10px", borderRadius: "4px", border: "1px solid #ccc" }}
        />
        <input
          type="password"
          placeholder="Xác nhận mật khẩu"
          value={passwordConfirmation}
          onChange={(e) => setPasswordConfirmation(e.target.value)}
          required
          style={{ padding: "10px", borderRadius: "4px", border: "1px solid #ccc" }}
        />
        <button type="submit" style={{ padding: "10px", backgroundColor: "#28a745", color: "white", border: "none", borderRadius: "4px", cursor: "pointer" }}>
          Đăng ký
        </button>
      </form>
      {error && <p style={{ color: "red", textAlign: "center", marginTop: "15px" }}>{error}</p>}
      <p style={{ textAlign: "center", marginTop: "15px" }}>
        Đã có tài khoản? <a href="/login">Đăng nhập ngay</a>
      </p>
    </div>
  );
}