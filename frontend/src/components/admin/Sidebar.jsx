import React from "react";
import { Link, useLocation } from "react-router-dom";
import { 
  LayoutDashboard, Users, Briefcase, Settings, 
  LogOut, ShieldCheck, Bell, ChevronRight 
} from "lucide-react";
import { cn } from "@/lib/utils";
import { Button } from "@/components/ui/button";

const Sidebar = () => {
  const location = useLocation();

  const menuItems = [
    { icon: LayoutDashboard, label: "Dashboard", path: "/admin/dashboard" },
    { icon: Users, label: "Người dùng", path: "/admin/users" },
    { icon: Briefcase, label: "Công việc", path: "/admin/jobs" },
    { icon: Bell, label: "Thông báo", path: "/admin/notifications" },
    { icon: Settings, label: "Cài đặt", path: "/admin/settings" },
  ];

  return (
    <div className="w-64 bg-slate-950 border-r border-slate-900 flex flex-col h-screen sticky top-0">
      <div className="p-6 flex items-center gap-3">
        <div className="bg-blue-600 p-2 rounded-lg">
          <ShieldCheck className="h-6 w-6 text-white" />
        </div>
        <span className="text-xl font-bold text-white tracking-tight">Admin Nova</span>
      </div>

      <nav className="flex-1 px-4 space-y-2 mt-4">
        {menuItems.map((item) => (
          <Link 
            key={item.path} 
            to={item.path}
            className={cn(
              "flex items-center justify-between px-3 py-2 rounded-lg transition-all duration-200 group",
              location.pathname === item.path 
                ? "bg-blue-600/10 text-blue-500" 
                : "text-slate-500 hover:text-slate-200 hover:bg-slate-900"
            )}
          >
            <div className="flex items-center gap-3">
              <item.icon className={cn(
                "h-5 w-5",
                location.pathname === item.path ? "text-blue-500" : "text-slate-500 group-hover:text-slate-300"
              )} />
              <span className="font-medium">{item.label}</span>
            </div>
            {location.pathname === item.path && <ChevronRight className="h-4 w-4" />}
          </Link>
        ))}
      </nav>

      <div className="p-4 mt-auto border-t border-slate-900">
        <div className="bg-slate-900/50 p-4 rounded-xl border border-slate-800 mb-4">
          <p className="text-xs text-slate-500 uppercase font-bold tracking-wider mb-2">Hỗ trợ kỹ thuật</p>
          <Button variant="link" className="p-0 h-auto text-blue-500 text-xs">Liên hệ Support</Button>
        </div>
        <Button 
          variant="ghost" 
          className="w-full justify-start text-slate-500 hover:text-red-400 hover:bg-red-400/10"
        >
          <LogOut className="h-5 w-5 mr-3" /> Đăng xuất
        </Button>
      </div>
    </div>
  );
};

export default Sidebar;
