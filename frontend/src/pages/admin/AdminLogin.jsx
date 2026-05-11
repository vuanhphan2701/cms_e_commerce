import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { adminApi } from "@/api/adminApi";

import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card";
import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
import { ShieldCheck, Lock, Mail, Eye, EyeOff, Loader2 } from "lucide-react";

const AdminLogin = () => {
  const navigate = useNavigate();
  const [step, setStep] = useState("LOGIN"); // LOGIN, VERIFY_2FA, SETUP_2FA
  const [formData, setFormData] = useState({
    email: "",
    password: "",
    two_factor_code: "",
    setup_secret: ""
  });
  const [showPassword, setShowPassword] = useState(false);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [qrCode, setQrCode] = useState("");

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
    setError("");
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setError("");

    try {
      const response = await adminApi.login(formData);

      if (response.status === 'success' || response.success) {
        if (response.data.requires_2fa_setup) {
          setQrCode(response.data.qr_code_image);
          setFormData({ ...formData, setup_secret: response.data.secret, two_factor_code: "" });
          setStep("SETUP_2FA");
        } else if (response.data.requires_2fa) {
          setFormData({ ...formData, two_factor_code: "" });
          setStep("VERIFY_2FA");
        } else if (response.data.access_token) {
          localStorage.setItem("adminToken", response.data.access_token);
          navigate("/admin/dashboard");
        }
      }
    } catch (err) {
      setError(
        err.response?.data?.message || "Đã có lỗi xảy ra. Vui lòng thử lại."
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-slate-950 p-4">
      <div className="w-full max-w-md animate-in fade-in zoom-in duration-300">
        <div className="flex justify-center mb-8">
          <div className="bg-blue-600 p-3 rounded-2xl shadow-lg shadow-blue-600/20">
            <ShieldCheck className="h-8 w-8 text-white" />
          </div>
        </div>

        <Card className="border-slate-800 bg-slate-900 shadow-2xl">
          <CardHeader className="space-y-1">
            <CardTitle className="text-2xl font-bold text-center text-white">
              {step === "LOGIN" && "Admin Login"}
              {step === "VERIFY_2FA" && "Xác thực 2FA"}
              {step === "SETUP_2FA" && "Cài đặt 2FA"}
            </CardTitle>
            <CardDescription className="text-center text-slate-400">
              {step === "LOGIN" && "Nhập thông tin để truy cập hệ thống quản trị"}
              {step === "VERIFY_2FA" && "Nhập mã xác thực từ ứng dụng Google Authenticator"}
              {step === "SETUP_2FA" && "Quét mã QR bên dưới để bắt đầu sử dụng 2FA"}
            </CardDescription>
          </CardHeader>
          <CardContent className="space-y-4">
            {error && (
              <Alert variant="destructive" className="bg-red-900/20 border-red-900/50 text-red-400 animate-in slide-in-from-top-2">
                <AlertCircle className="h-4 w-4" />
                <AlertTitle>Lỗi</AlertTitle>
                <AlertDescription>{error}</AlertDescription>
              </Alert>
            )}

            <form onSubmit={handleSubmit} className="space-y-4">
              {step === "LOGIN" && (
                <>
                  <div className="space-y-2">
                    <Label htmlFor="email" className="text-slate-300">Email</Label>
                    <div className="relative">
                      <Mail className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
                      <Input
                        id="email"
                        name="email"
                        type="email"
                        placeholder="admin@example.com"
                        required
                        value={formData.email}
                        onChange={handleChange}
                        className="bg-slate-950 border-slate-800 pl-10 text-slate-200 focus:ring-blue-600"
                      />
                    </div>
                  </div>
                  <div className="space-y-2">
                    <div className="flex items-center justify-between">
                      <Label htmlFor="password" className="text-slate-300">Mật khẩu</Label>
                    </div>
                    <div className="relative">
                      <Lock className="absolute left-3 top-3 h-4 w-4 text-slate-500" />
                      <Input
                        id="password"
                        name="password"
                        type={showPassword ? "text" : "password"}
                        required
                        value={formData.password}
                        onChange={handleChange}
                        className="bg-slate-950 border-slate-800 pl-10 text-slate-200 focus:ring-blue-600"
                      />
                      <button
                        type="button"
                        onClick={() => setShowPassword(!showPassword)}
                        className="absolute right-3 top-3 text-slate-500 hover:text-slate-300"
                      >
                        {showPassword ? <EyeOff className="h-4 w-4" /> : <Eye className="h-4 w-4" />}
                      </button>
                    </div>
                  </div>
                </>
              )}

              {(step === "VERIFY_2FA" || step === "SETUP_2FA") && (
                <div className="space-y-4">
                  {step === "SETUP_2FA" && qrCode && (
                    <div className="flex flex-col items-center gap-4 bg-white p-4 rounded-xl mx-auto w-fit">
                      <img src={qrCode} alt="2FA QR Code" className="w-48 h-48" />
                      <p className="text-xs text-slate-900 font-mono font-bold">{formData.setup_secret}</p>
                    </div>
                  )}
                  <div className="space-y-2">
                    <Label htmlFor="two_factor_code" className="text-slate-300 text-center block">Mã xác thực 6 số</Label>
                    <Input
                      id="two_factor_code"
                      name="two_factor_code"
                      type="text"
                      inputMode="numeric"
                      pattern="[0-9]*"
                      maxLength="6"
                      placeholder="000000"
                      required
                      autoFocus
                      value={formData.two_factor_code}
                      onChange={handleChange}
                      className="bg-slate-950 border-slate-800 text-center text-2xl tracking-[0.5em] h-14 text-blue-400 font-bold focus:ring-blue-600"
                    />
                  </div>
                </div>
              )}

              <Button 
                type="submit" 
                className="w-full h-11 bg-blue-600 hover:bg-blue-500 text-white font-semibold transition-all shadow-lg shadow-blue-600/20"
                disabled={loading}
              >
                {loading ? (
                  <Loader2 className="h-5 w-5 animate-spin" />
                ) : (
                  step === "LOGIN" ? "Đăng nhập" : "Xác nhận"
                )}
              </Button>
            </form>
          </CardContent>
          <CardFooter>
            {step !== "LOGIN" && (
              <Button 
                variant="ghost" 
                onClick={() => setStep("LOGIN")}
                className="w-full text-slate-400 hover:text-slate-200 hover:bg-slate-800"
              >
                Quay lại đăng nhập
              </Button>
            )}
          </CardFooter>
        </Card>
        
        <p className="mt-8 text-center text-sm text-slate-500">
          Hệ thống bảo mật bởi 2FA. Nếu gặp sự cố vui lòng liên hệ kỹ thuật.
        </p>
      </div>
    </div>
  );
};

export default AdminLogin;

import { AlertCircle } from "lucide-react";
