import React from "react";
import { Outlet } from "react-router-dom";
import Sidebar from "../admin/Sidebar";
import { Search, Bell, UserCircle } from "lucide-react";
import { Input } from "@/components/ui/input";

const AdminLayout = () => {
  return (
    <div className="flex min-h-screen bg-slate-950">
      <Sidebar />
      <main className="flex-1 flex flex-col">
        {/* Top Header */}
        <header className="h-16 border-b border-slate-900 flex items-center justify-between px-8 bg-slate-950/50 backdrop-blur-xl sticky top-0 z-10">
          <div className="relative w-96">
            <Search className="absolute left-3 top-2.5 h-4 w-4 text-slate-500" />
            <Input 
              placeholder="Tìm kiếm hệ thống..." 
              className="bg-slate-900/50 border-slate-800 pl-10 h-9 text-slate-300 focus:ring-blue-600"
            />
          </div>
          
          <div className="flex items-center gap-4">
            <button className="h-9 w-9 flex items-center justify-center rounded-lg border border-slate-800 text-slate-400 hover:text-white hover:bg-slate-900 transition-colors">
              <Bell className="h-5 w-5" />
            </button>
            <div className="h-9 w-px bg-slate-900 mx-2" />
            <div className="flex items-center gap-3 pl-2 group cursor-pointer">
              <div className="text-right hidden md:block">
                <p className="text-sm font-bold text-slate-200 group-hover:text-blue-400 transition-colors">Admin Super</p>
                <p className="text-[10px] text-slate-500 uppercase font-bold tracking-widest">Super Admin</p>
              </div>
              <div className="h-10 w-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center border border-blue-400/20 shadow-lg shadow-blue-600/20">
                <UserCircle className="h-6 w-6 text-white" />
              </div>
            </div>
          </div>
        </header>

        {/* Page Content */}
        <div className="p-4 md:p-8">
          <Outlet />
        </div>
      </main>
    </div>
  );
};

export default AdminLayout;
