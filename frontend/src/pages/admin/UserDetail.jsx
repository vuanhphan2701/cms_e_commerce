import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { adminApi } from "@/api/adminApi";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import {
  ArrowLeft, CheckCircle2, XCircle, Ban,
  MapPin, Phone, Mail, Calendar, Star, FileText,
  CreditCard, MessageSquare, ShieldCheck, AlertTriangle, Loader2, ExternalLink,
  History, User as UserIcon, Briefcase, Wallet
} from "lucide-react";
import { format } from "date-fns";
import { useToast } from "@/hooks/use-toast";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";

const UserDetail = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { toast } = useToast();
  const [user, setUser] = useState(null);
  const [activities, setActivities] = useState([]);
  const [loading, setLoading] = useState(true);
  const [actionLoading, setActionLoading] = useState(false);

  useEffect(() => {
    fetchData();
  }, [id]);

  const fetchData = async () => {
    setLoading(true);
    try {
      const [userRes, activityRes] = await Promise.all([
        adminApi.getUser(id),
        adminApi.getUserActivity(id)
      ]);

      if (userRes.status === 'success') {
        setUser(userRes.data);
      }

      if (activityRes.status === 'success') {
        setActivities(Array.isArray(activityRes.data) ? activityRes.data : []);
      }

    } catch (error) {
      console.error("Fetch Error:", error);
      toast({
        variant: "destructive",
        title: "Lỗi hệ thống",
        description: "Không thể tải dữ liệu người dùng."
      });
    } finally {
      setLoading(false);
    }
  };

  const handleKYC = async (approve) => {
    setActionLoading(true);
    try {
      await adminApi.verifyUserKYC(id, {
        approve,
        reason: approve ? "" : "Ảnh CCCD không rõ nét hoặc không trùng khớp."
      });

      toast({
        variant: "success",
        title: "Thành công",
        description: `Đã ${approve ? 'duyệt' : 'từ chối'} xác thực danh tính.`,
      });
      fetchData();
    } catch (error) {
      toast({
        variant: "destructive",
        title: "Thao tác thất bại",
        description: error.response?.data?.message || "Có lỗi xảy ra."
      });
    } finally {
      setActionLoading(false);
    }
  };

  const handleStatusUpdate = async (status) => {
    setActionLoading(true);
    try {
      await adminApi.updateUserStatus(id, {
        status,
        reason: "Vi phạm điều khoản hệ thống.",
        days: status === 'suspended' ? 7 : null
      });

      toast({
        variant: "success",
        title: "Thành công",
        description: `Đã cập nhật trạng thái người dùng.`,
      });
      fetchData();
    } catch (error) {
      toast({
        variant: "destructive",
        title: "Lỗi thực thi",
        description: error.response?.data?.message || "Có lỗi xảy ra."
      });
    } finally {
      setActionLoading(false);
    }
  };

  const openZalo = () => {
    if (!user?.phone) {
      toast({
        variant: "destructive",
        title: "Không có số điện thoại",
        description: "Người dùng này chưa cập nhật số điện thoại."
      });
      return;
    }
    const cleanPhone = user.phone.replace(/\D/g, '');
    window.open(`https://zalo.me/${cleanPhone}`, '_blank');
  };

  if (loading) return (
    <div className="p-8 flex flex-col items-center justify-center min-h-[400px] text-slate-400">
      <Loader2 className="h-10 w-10 animate-spin mb-4 text-blue-500" />
      <span className="animate-pulse">Đang tải hồ sơ chuyên sâu...</span>
    </div>
  );

  if (!user) return <div className="p-8 text-white text-center">Không tìm thấy dữ liệu người dùng.</div>;

  return (
    <div className="p-6 space-y-6 max-w-7xl mx-auto animate-in fade-in duration-500">
      <div className="flex items-center gap-4">
        <Button variant="ghost" size="icon" onClick={() => navigate(-1)} className="text-slate-400 hover:text-white hover:bg-slate-800">
          <ArrowLeft className="h-5 w-5" />
        </Button>
        <h1 className="text-2xl font-bold text-white tracking-tight">Hồ sơ người dùng</h1>
        {user.is_verified ? (
          <Badge className="bg-emerald-500/20 text-emerald-400 border-emerald-500/40 px-3 py-1">
            <ShieldCheck className="h-3 w-3 mr-1" /> Đã xác thực
          </Badge>
        ) : (
          <Badge variant="outline" className="text-slate-500 border-slate-800 px-3 py-1">
            Chưa xác thực
          </Badge>
        )}
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Left Column: Info & Critical Actions */}
        <div className="lg:col-span-1 space-y-6">
          <Card className="bg-slate-900 border-slate-800 shadow-2xl overflow-hidden">
            <CardHeader className="flex flex-col items-center text-center pb-2">
              <div className="h-32 w-32 rounded-full bg-slate-800 border-4 border-slate-950 shadow-2xl overflow-hidden mb-4 ring-2 ring-slate-800">
                {user.avatar ? <img src={user.avatar} alt="" className="w-full h-full object-cover" /> : <div className="h-full w-full flex items-center justify-center text-5xl font-bold text-slate-700 bg-slate-800">{user.name.charAt(0)}</div>}
              </div>
              <CardTitle className="text-2xl text-white font-black">{user.name}</CardTitle>
              <CardDescription className="text-slate-400 font-medium">{user.email}</CardDescription>

              <div className="flex gap-2 mt-4">
                <Badge className="bg-blue-600/20 text-blue-400 border-blue-500/30 px-3">{user.role}</Badge>
                <Badge className={`px-3 ${user.status === 'active' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400'}`}>
                  {user.status === 'active' ? 'Hoạt động' : (user.status === 'banned' ? 'Bị khóa' : 'Tạm khóa')}
                </Badge>
              </div>
            </CardHeader>

            <CardContent className="p-6 pt-2 space-y-4">
              <Button
                className={`w-full justify-center font-black h-12 transition-all shadow-xl ${user?.phone
                    ? "bg-[#0068ff] text-white hover:bg-[#0056d2] scale-105"
                    : "bg-slate-800 text-slate-500"
                  }`}
                onClick={openZalo}
              >
                <MessageSquare className="h-5 w-5 mr-2" />
                {user?.phone ? "LIÊN HỆ QUA ZALO" : "CHƯA CÓ SỐ ZALO"}
                {user?.phone && <ExternalLink className="h-3 w-3 ml-2 opacity-50" />}
              </Button>

              <div className="pt-4 border-t border-slate-800 space-y-3">
                <div className="flex items-center gap-3 text-slate-300">
                  <Phone className="h-4 w-4 text-slate-500" />
                  <span className="text-sm font-bold">{user.phone || "Chưa cập nhật"}</span>
                </div>
                <div className="flex items-center gap-3 text-slate-300">
                  <MapPin className="h-4 w-4 text-slate-500" />
                  <span className="text-sm font-medium">{user.city || "N/A"}</span>
                </div>
                <div className="flex items-center gap-3 text-slate-300">
                  <Calendar className="h-4 w-4 text-slate-500" />
                  <span className="text-sm">Tham gia: {format(new Date(user.created_at), "dd/MM/yyyy")}</span>
                </div>
              </div>
            </CardContent>
          </Card>

          <Card className="bg-slate-900 border-slate-800">
            <CardHeader className="bg-slate-950/30 py-3">
              <CardTitle className="text-xs font-bold text-slate-500 uppercase tracking-tighter">Hành động quản trị</CardTitle>
            </CardHeader>
            <CardContent className="p-4 space-y-2">
              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <Button variant="outline" className="w-full justify-start text-amber-500 border-amber-900/20 bg-amber-950/10 hover:bg-amber-900/20 font-bold">
                    <AlertTriangle className="h-4 w-4 mr-3" /> {user.status === 'active' ? 'Tạm đình chỉ' : 'Kích hoạt lại'}
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent className="bg-slate-900 border-slate-800 text-white shadow-2xl">
                  <AlertDialogHeader><AlertDialogTitle>Xác nhận thay đổi trạng thái?</AlertDialogTitle><AlertDialogDescription className="text-slate-400">Hành động này sẽ gửi thông báo hệ thống cho người dùng.</AlertDialogDescription></AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel className="bg-slate-800 border-slate-700 text-white">Hủy</AlertDialogCancel>
                    <AlertDialogAction className="bg-amber-600 hover:bg-amber-500" onClick={() => handleStatusUpdate(user.status === 'active' ? 'suspended' : 'active')}>Xác nhận</AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>

              <AlertDialog>
                <AlertDialogTrigger asChild>
                  <Button variant="outline" className="w-full justify-start text-red-500 border-red-900/20 bg-red-950/10 hover:bg-red-900/20 font-bold" disabled={user.status === 'banned'}>
                    <Ban className="h-4 w-4 mr-3" /> Khóa vĩnh viễn (BAN)
                  </Button>
                </AlertDialogTrigger>
                <AlertDialogContent className="bg-slate-900 border-slate-800 text-white shadow-2xl">
                  <AlertDialogHeader><AlertDialogTitle className="text-red-500">CẢNH BÁO: KHÓA VĨNH VIỄN</AlertDialogTitle><AlertDialogDescription className="text-slate-400">Người dùng sẽ bị tước quyền truy cập mãi mãi. Thao tác không thể hoàn tác.</AlertDialogDescription></AlertDialogHeader>
                  <AlertDialogFooter>
                    <AlertDialogCancel className="bg-slate-800 border-slate-700 text-white">Hủy</AlertDialogCancel>
                    <AlertDialogAction className="bg-red-600 hover:bg-red-500" onClick={() => handleStatusUpdate('banned')}>Xác nhận BAN</AlertDialogAction>
                  </AlertDialogFooter>
                </AlertDialogContent>
              </AlertDialog>
            </CardContent>
          </Card>
        </div>

        {/* Right Column: Tabs System */}
        <div className="lg:col-span-2 space-y-6">
          <Tabs defaultValue="kyc" className="w-full">
            <TabsList className="bg-slate-900/50 border border-slate-800 w-full justify-start p-1 h-auto mb-6 backdrop-blur-sm">
              <TabsTrigger value="kyc" className="data-[state=active]:bg-slate-800 data-[state=active]:text-white py-3 px-6 gap-2 font-bold transition-all">
                <CreditCard className="h-4 w-4" /> Xác minh danh tính
              </TabsTrigger>
              <TabsTrigger value="activity" className="data-[state=active]:bg-slate-800 data-[state=active]:text-white py-3 px-6 gap-2 font-bold transition-all">
                <History className="h-4 w-4" /> Nhật ký hệ thống
              </TabsTrigger>
              <TabsTrigger value="jobs" className="data-[state=active]:bg-slate-800 data-[state=active]:text-white py-3 px-6 gap-2 font-bold transition-all">
                <Briefcase className="h-4 w-4" /> Công việc
              </TabsTrigger>
            </TabsList>

            <TabsContent value="kyc" className="animate-in slide-in-from-right-2 duration-300">
              <Card className="bg-slate-900 border-slate-800 shadow-xl overflow-hidden ring-1 ring-slate-800">
                <CardHeader className="flex flex-row items-center justify-between bg-slate-950/50 border-b border-slate-800 p-6">
                  <div>
                    <CardTitle className="text-xl text-white font-bold">Hồ sơ CCCD / Passport</CardTitle>
                    <CardDescription className="text-slate-400">Kiểm tra tính pháp lý của giấy tờ tùy thân.</CardDescription>
                  </div>
                  {!user.is_verified && (
                    <div className="flex gap-2">
                      <Button size="sm" className="bg-emerald-600 hover:bg-emerald-500 text-white font-bold px-6 shadow-lg shadow-emerald-900/20" onClick={() => handleKYC(true)}>DUYỆT</Button>
                      <Button size="sm" variant="destructive" className="font-bold px-6" onClick={() => handleKYC(false)}>TỪ CHỐI</Button>
                    </div>
                  )}
                </CardHeader>
                <CardContent className="p-6">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div className="space-y-3">
                      <p className="text-xs font-black text-slate-500 uppercase tracking-tighter">Mặt trước giấy tờ</p>
                      <div className="group relative aspect-video bg-slate-950 rounded-2xl border-2 border-dashed border-slate-800 flex items-center justify-center overflow-hidden transition-all hover:border-blue-500/50 cursor-zoom-in">
                        {user.identity_card_front ? (
                          <img src={user.identity_card_front} className="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110" alt="ID Front" />
                        ) : (
                          <div className="flex flex-col items-center text-slate-700">
                            <FileText className="h-10 w-10 mb-2 opacity-20" />
                            <span className="text-xs font-bold uppercase">Trống</span>
                          </div>
                        )}
                      </div>
                    </div>
                    <div className="space-y-3">
                      <p className="text-xs font-black text-slate-500 uppercase tracking-tighter">Mặt sau giấy tờ</p>
                      <div className="group relative aspect-video bg-slate-950 rounded-2xl border-2 border-dashed border-slate-800 flex items-center justify-center overflow-hidden transition-all hover:border-blue-500/50 cursor-zoom-in">
                        {user.identity_card_back ? (
                          <img src={user.identity_card_back} className="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110" alt="ID Back" />
                        ) : (
                          <div className="flex flex-col items-center text-slate-700">
                            <FileText className="h-10 w-10 mb-2 opacity-20" />
                            <span className="text-xs font-bold uppercase">Trống</span>
                          </div>
                        )}
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="activity" className="animate-in slide-in-from-right-2 duration-300">
              <Card className="bg-slate-900 border-slate-800 shadow-xl">
                <CardHeader className="p-6">
                  <CardTitle className="text-white font-bold flex items-center gap-2">
                    <History className="h-5 w-5 text-blue-500" /> Nhật ký quản trị viên
                  </CardTitle>
                </CardHeader>
                <CardContent className="px-6 pb-6">
                  <div className="space-y-8">
                    {activities && activities.length > 0 ? activities.map((log) => (
                      <div key={log.id} className="relative pl-8 border-l-2 border-slate-800 pb-2 group">
                        <div className="absolute -left-[9px] top-0 h-4 w-4 rounded-full bg-slate-900 border-2 border-slate-800 group-hover:border-blue-500 transition-colors shadow-2xl" />
                        <div className="flex flex-col gap-2">
                          <div className="flex items-center justify-between">
                            <span className="text-sm font-black text-slate-200 uppercase tracking-tight">{log.action}</span>
                            <Badge className="text-[9px] font-black bg-slate-800 text-slate-400 border-none">{format(new Date(log.created_at), "HH:mm")}</Badge>
                          </div>
                          <div className="flex items-center gap-3 text-[11px] text-slate-500 font-bold">
                            <div className="flex items-center gap-1">
                              <UserIcon className="h-3 w-3" />
                              <span className="text-blue-400">{log.admin?.name || 'Hệ thống'}</span>
                            </div>
                            <span>•</span>
                            <div className="flex items-center gap-1">
                              <Calendar className="h-3 w-3" />
                              <span>{format(new Date(log.created_at), "dd/MM/yyyy")}</span>
                            </div>
                          </div>
                        </div>
                      </div>
                    )) : (
                      <div className="flex flex-col items-center justify-center py-20 text-slate-600">
                        <History className="h-12 w-12 mb-4 opacity-10" />
                        <p className="font-bold italic">Chưa ghi nhận hoạt động nào</p>
                      </div>
                    )}
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            <TabsContent value="jobs" className="mt-6">
              <Card className="bg-slate-900 border-slate-800 p-20 text-center text-slate-700 font-bold italic ring-1 ring-slate-800">
                <Briefcase className="h-10 w-10 mx-auto mb-4 opacity-10" />
                Dữ liệu bài đăng sẽ hiển thị sau khi hoàn thiện Module Jobs.
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </div>
    </div>
  );
};

export default UserDetail;
