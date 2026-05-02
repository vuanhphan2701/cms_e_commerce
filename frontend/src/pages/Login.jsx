import React, { useState } from "react";
import { login, resendVerification } from "../api/authApi";
import { useNavigate, Link } from "react-router-dom";
import { useAlert } from "../components/common/AlertContext";

export default function Login() {
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPassword, setShowPassword] = useState(false);
  const [error, setError] = useState("");
  const [errorType, setErrorType] = useState("");
  const [loading, setLoading] = useState(false);
  const [resendingEmail, setResendingEmail] = useState(false);
  const [retryCountdown, setRetryCountdown] = useState(0);
  const navigate = useNavigate();
  const { showAlert } = useAlert();

  // Countdown timer for rate limit
  const startCountdown = (seconds) => {
    setRetryCountdown(seconds);
    const interval = setInterval(() => {
      setRetryCountdown((prev) => {
        if (prev <= 1) {
          clearInterval(interval);
          return 0;
        }
        return prev - 1;
      });
    }, 1000);
  };

  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");
    setErrorType("");
    setLoading(true);

    try {
      const response = await login({ email, password });
      const token = response?.data?.access_token;

      if (token) {
        localStorage.setItem("token", token);
        showAlert("Đăng nhập thành công! Chào mừng bạn trở lại.", "success");
        navigate("/");
      } else {
        setError("Không tìm thấy token xác thực từ server.");
      }
    } catch (err) {
      const data = err.response?.data;
      const status = err.response?.status;
      const message = data?.message || "Đăng nhập thất bại. Vui lòng kiểm tra lại thông tin.";
      const type = data?.data?.type || "";

      // If email is not verified, redirect to OTP page
      if (status === 403 || type === 'email_not_verified') {
        showAlert("Vui lòng xác thực email trước khi đăng nhập.", "warning");
        navigate(`/verify-otp?email=${encodeURIComponent(email)}`);
        return;
      }

      setError(message);
      setErrorType(type);

      // Start countdown for rate limit
      if (status === 429 && data?.data?.retry_after) {
        startCountdown(data.data.retry_after);
      }
    } finally {
      setLoading(false);
    }
  };

  const handleResendVerification = async () => {
    // Need to login first to get a temp token — but user can't login without verification
    // So we show them a message to check email or go to resend page
    setResendingEmail(true);
    try {
      showAlert("Vui lòng kiểm tra hộp thư email để xác thực tài khoản.", "success");
    } finally {
      setResendingEmail(false);
    }
  };

  // Error icon based on type
  const getErrorIcon = () => {
    switch (errorType) {
      case "rate_limit":
        return (
          <svg className="w-5 h-5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clipRule="evenodd" />
          </svg>
        );
      case "account_locked":
        return (
          <svg className="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clipRule="evenodd" />
          </svg>
        );
      case "email_not_verified":
        return (
          <svg className="w-5 h-5 text-blue-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
          </svg>
        );
      default:
        return (
          <svg className="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clipRule="evenodd" />
          </svg>
        );
    }
  };

  const getErrorBgClass = () => {
    switch (errorType) {
      case "rate_limit":
        return "bg-amber-50 border-amber-200";
      case "account_locked":
        return "bg-red-50 border-red-200";
      case "email_not_verified":
        return "bg-blue-50 border-blue-200";
      default:
        return "bg-red-50 border-red-100";
    }
  };

  const getErrorTextClass = () => {
    switch (errorType) {
      case "rate_limit":
        return "text-amber-800";
      case "account_locked":
        return "text-red-800";
      case "email_not_verified":
        return "text-blue-800";
      default:
        return "text-red-800";
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex flex-col justify-center py-12 sm:px-6 lg:px-8 relative overflow-hidden">
      {/* Background decoration */}
      <div className="absolute inset-0 overflow-hidden pointer-events-none">
        <div className="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div className="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-500/5 rounded-full blur-3xl"></div>
      </div>

      <div className="sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div className="flex justify-center">
          <div className="h-14 w-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-blue-500/30 ring-1 ring-white/10">
            <span className="text-white font-bold text-xl tracking-tight">CMS</span>
          </div>
        </div>
        <h2 className="mt-6 text-center text-3xl font-bold text-white tracking-tight">
          Đăng nhập hệ thống
        </h2>
        <p className="mt-2 text-center text-sm text-slate-400">
          Hoặc{" "}
          <Link
            to="/register"
            className="font-semibold text-blue-400 hover:text-blue-300 transition-colors"
          >
            đăng ký tài khoản mới
          </Link>
        </p>
      </div>

      <div className="mt-8 sm:mx-auto sm:w-full sm:max-w-md relative z-10">
        <div className="bg-white/5 backdrop-blur-xl py-8 px-4 shadow-2xl sm:rounded-2xl sm:px-10 border border-white/10 ring-1 ring-white/5">
          <form className="space-y-6" onSubmit={handleLogin}>
            {/* Error Message */}
            {error && (
              <div className={`rounded-xl p-4 border ${getErrorBgClass()} animate-shake`}>
                <div className="flex items-start gap-3">
                  {getErrorIcon()}
                  <div className="flex-1">
                    <p className={`text-sm font-medium ${getErrorTextClass()}`}>{error}</p>

                    {/* Rate limit countdown */}
                    {errorType === "rate_limit" && retryCountdown > 0 && (
                      <div className="mt-2 flex items-center gap-2">
                        <div className="h-1.5 flex-1 bg-amber-200 rounded-full overflow-hidden">
                          <div
                            className="h-full bg-amber-500 rounded-full transition-all duration-1000"
                            style={{ width: `${(retryCountdown / 60) * 100}%` }}
                          ></div>
                        </div>
                        <span className="text-xs font-mono text-amber-700 tabular-nums">
                          {retryCountdown}s
                        </span>
                      </div>
                    )}

                    {/* Email not verified action */}
                    {errorType === "email_not_verified" && (
                      <p className="mt-2 text-xs text-blue-600">
                        Vui lòng kiểm tra hộp thư email (bao gồm thư rác) để xác thực tài khoản.
                      </p>
                    )}

                    {/* Account locked — suggest password reset */}
                    {errorType === "account_locked" && (
                      <Link
                        to="/forgot-password"
                        className="mt-2 inline-block text-xs font-medium text-red-700 underline underline-offset-2 hover:text-red-600"
                      >
                        Đặt lại mật khẩu để mở khóa tài khoản →
                      </Link>
                    )}
                  </div>
                </div>
              </div>
            )}

            {/* Email field */}
            <div>
              <label htmlFor="login-email" className="block text-sm font-medium text-slate-300">
                Địa chỉ Email
              </label>
              <div className="mt-1.5">
                <input
                  id="login-email"
                  name="email"
                  type="email"
                  autoComplete="email"
                  required
                  value={email}
                  onChange={(e) => setEmail(e.target.value)}
                  className="appearance-none block w-full px-4 py-3 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 sm:text-sm transition-all duration-200"
                  placeholder="admin@example.com"
                />
              </div>
            </div>

            {/* Password field */}
            <div>
              <div className="flex items-center justify-between">
                <label htmlFor="login-password" className="block text-sm font-medium text-slate-300">
                  Mật khẩu
                </label>
                <Link
                  to="/forgot-password"
                  className="text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors"
                >
                  Quên mật khẩu?
                </Link>
              </div>
              <div className="mt-1.5 relative">
                <input
                  id="login-password"
                  name="password"
                  type={showPassword ? "text" : "password"}
                  autoComplete="current-password"
                  required
                  value={password}
                  onChange={(e) => setPassword(e.target.value)}
                  className="appearance-none block w-full px-4 py-3 pr-12 bg-white/5 border border-white/10 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 sm:text-sm transition-all duration-200"
                  placeholder="••••••••"
                />
                <button
                  type="button"
                  onClick={() => setShowPassword(!showPassword)}
                  className="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-300 transition-colors"
                >
                  {showPassword ? (
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>
                  ) : (
                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={1.5}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                      <path strokeLinecap="round" strokeLinejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                  )}
                </button>
              </div>
            </div>

            {/* Submit button */}
            <div>
              <button
                type="submit"
                disabled={loading || retryCountdown > 0}
                className={`w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-xl shadow-lg text-sm font-semibold text-white transition-all duration-200 ${
                  loading || retryCountdown > 0
                    ? "bg-slate-600 cursor-not-allowed opacity-60"
                    : "bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-blue-500/25 hover:shadow-blue-500/40 active:scale-[0.98]"
                }`}
              >
                {loading ? (
                  <>
                    <svg className="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4"></circle>
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang xử lý...
                  </>
                ) : retryCountdown > 0 ? (
                  `Vui lòng đợi ${retryCountdown}s`
                ) : (
                  "Đăng nhập"
                )}
              </button>
            </div>
          </form>
        </div>

        {/* Footer */}
        <p className="mt-6 text-center text-xs text-slate-500">
          Bảo mật bởi JWT Authentication & Rate Limiting
        </p>
      </div>
    </div>
  );
}
