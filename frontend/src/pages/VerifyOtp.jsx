import React, { useState, useEffect } from "react";
import { useNavigate, useSearchParams, Link } from "react-router-dom";
import { verifyOtp, resendVerification } from "../api/authApi";
import { useAlert } from "../components/common/AlertContext";
import { EnvelopeIcon, KeyIcon } from "@heroicons/react/24/outline";

const VerifyOtp = () => {
  const navigate = useNavigate();
  const [searchParams] = useSearchParams();
  const email = searchParams.get("email");
  const { showAlert } = useAlert();

  const [otp, setOtp] = useState("");
  const [loading, setLoading] = useState(false);
  const [resendLoading, setResendLoading] = useState(false);
  const [countdown, setCountdown] = useState(60);

  useEffect(() => {
    if (!email) {
      navigate("/login");
    }

    // Start countdown for resend
    let timer;
    if (countdown > 0) {
      timer = setTimeout(() => setCountdown(countdown - 1), 1000);
    }
    return () => clearTimeout(timer);
  }, [email, navigate, countdown]);

  const handleSubmit = async (e) => {
    e.preventDefault();

    if (otp.length !== 6) {
      showAlert("Mã xác thực phải gồm 6 chữ số.", "error");
      return;
    }

    setLoading(true);

    try {
      const response = await verifyOtp({ email, otp });
      
      // Save token and navigate to dashboard
      if (response.data && response.data.access_token) {
        localStorage.setItem("token", response.data.access_token);
        showAlert("Xác thực email thành công!", "success");
        navigate("/");
      } else {
        // Fallback if no token is returned
        showAlert("Xác thực thành công! Vui lòng đăng nhập.", "success");
        navigate("/login");
      }
    } catch (err) {
      const data = err.response?.data;
      showAlert(data?.message || "Mã xác thực không hợp lệ. Vui lòng thử lại.", "error");
    } finally {
      setLoading(false);
    }
  };

  const handleResend = async () => {
    if (countdown > 0) return;

    setResendLoading(true);

    try {
      await resendVerification(email);
      showAlert("Đã gửi mã xác thực mới đến email của bạn.", "success");
      setCountdown(60); // Reset timer
    } catch (err) {
      const data = err.response?.data;
      showAlert(data?.message || "Không thể gửi mã. Vui lòng thử lại sau.", "error");
    } finally {
      setResendLoading(false);
    }
  };

  return (
    <div className="min-h-screen flex flex-col justify-center items-center bg-gray-900 px-4 relative overflow-hidden">
      {/* Background decorations */}
      <div className="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
        <div className="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-blue-600/20 blur-[120px]"></div>
        <div className="absolute bottom-[10%] -right-[10%] w-[40%] h-[40%] rounded-full bg-purple-600/20 blur-[100px]"></div>
      </div>

      <div className="w-full max-w-md bg-gray-800/60 backdrop-blur-xl p-8 rounded-2xl shadow-2xl border border-gray-700/50 z-10">
        <div className="text-center mb-8">
          <div className="w-16 h-16 bg-blue-500/20 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-inner shadow-blue-500/20">
            <EnvelopeIcon className="h-8 w-8 text-blue-400" />
          </div>
          <h2 className="text-3xl font-bold text-white mb-2">Xác thực Email</h2>
          <p className="text-gray-400">
            Vui lòng nhập mã OTP 6 số đã được gửi đến email <br />
            <span className="font-semibold text-blue-400">{email}</span>
          </p>
        </div>

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block text-sm font-medium text-gray-300 mb-2">
              Mã xác thực OTP
            </label>
            <div className="relative">
              <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <KeyIcon className="h-5 w-5 text-gray-400" />
              </div>
              <input
                type="text"
                value={otp}
                onChange={(e) => {
                  const val = e.target.value.replace(/[^0-9]/g, ''); // Only allow numbers
                  if (val.length <= 6) setOtp(val);
                }}
                className="w-full pl-10 pr-4 py-3 bg-gray-900/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-500 transition-all text-center tracking-[0.5em] font-mono text-xl"
                placeholder="000000"
                required
                autoFocus
              />
            </div>
          </div>

          <button
            type="submit"
            disabled={loading || otp.length !== 6}
            className={`w-full py-3 px-4 flex justify-center items-center rounded-xl text-sm font-semibold text-white transition-all shadow-lg
              ${
                loading || otp.length !== 6
                  ? "bg-blue-600/50 cursor-not-allowed"
                  : "bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 hover:shadow-blue-500/25"
              }
            `}
          >
            {loading ? (
              <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
            ) : (
              "Xác thực"
            )}
          </button>
        </form>

        <div className="mt-6 text-center">
          <p className="text-sm text-gray-400">
            Chưa nhận được mã?{" "}
            <button
              onClick={handleResend}
              disabled={countdown > 0 || resendLoading}
              className={`font-semibold ${
                countdown > 0
                  ? "text-gray-500 cursor-not-allowed"
                  : "text-blue-400 hover:text-blue-300"
              }`}
            >
              {resendLoading
                ? "Đang gửi..."
                : countdown > 0
                ? `Gửi lại sau (${countdown}s)`
                : "Gửi lại mã"}
            </button>
          </p>
        </div>

        <div className="mt-8 pt-6 border-t border-gray-700/50 text-center">
          <Link
            to="/login"
            className="text-sm text-gray-400 hover:text-white transition-colors"
          >
            &larr; Quay lại đăng nhập
          </Link>
        </div>
      </div>
    </div>
  );
};

export default VerifyOtp;
