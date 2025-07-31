import { Outlet } from "react-router-dom";
import Sidebar from "../components/student/Sidebar";
import { useState } from "react";
import { Menu, X } from "lucide-react";

const StudentLayout = () => {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className="flex min-h-screen bg-gray-50">
      {/* Mobile menu button */}
      <div className="lg:hidden fixed top-4 left-4 z-50">
        <button
          onClick={() => setSidebarOpen(!sidebarOpen)}
          className="p-2 bg-white rounded-md shadow-md border border-gray-200"
        >
          {sidebarOpen ? (
            <X className="w-6 h-6 text-gray-600" />
          ) : (
            <Menu className="w-6 h-6 text-gray-600" />
          )}
        </button>
      </div>

      {/* Sidebar */}
      <div className={`${sidebarOpen ? 'translate-x-0' : '-translate-x-full'} lg:translate-x-0 fixed lg:static inset-y-0 left-0 z-40 transition-transform duration-300 ease-in-out`}>
        <Sidebar onItemClick={() => setSidebarOpen(false)} />
      </div>

      {/* Overlay for mobile */}
      {sidebarOpen && (
        <div 
          className="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-30"
          onClick={() => setSidebarOpen(false)}
        />
      )}

      <div className="flex-1 lg:ml-0">
        {/* Top Header with User Avatar */}
        <header className="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-4 ml-0 lg:ml-0">
          <div className="flex justify-between items-center">
            <div className="lg:hidden w-8"></div> {/* Spacer for mobile menu button */}
            <div className="flex justify-end items-center ml-auto">
              <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gray-300 rounded-full flex items-center justify-center">
                <span className="text-gray-600 font-semibold text-xs sm:text-sm">S</span>
              </div>
            </div>
          </div>
        </header>

        {/* Page Content */}
        <main className="flex-1 p-4 sm:p-6 bg-gray-100">
          <Outlet />
        </main>
      </div>
    </div>
  );
};

export default StudentLayout;
