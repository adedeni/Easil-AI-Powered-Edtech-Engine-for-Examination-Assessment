import React, { useState } from "react";
import { Outlet, NavLink, useNavigate } from "react-router-dom";
import { 
  Home,
  GraduationCap,
  Users,
  BookOpen,
  BarChart3,
  LogOut,
  Menu,
  X
} from "lucide-react";

export default function AdminLayout() {
  const navigate = useNavigate();
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  const menuItems = [
    { path: "/admin", icon: Home, label: "Home" },
    { path: "/admin/results", icon: BarChart3, label: "Results" },
    { path: "/admin/lecturers", icon: GraduationCap, label: "Lecturers" },
    { path: "/admin/students", icon: Users, label: "Students" },
    { path: "/admin/courses", icon: BookOpen, label: "Courses" }
  ];

  const handleLogout = () => {
    // Add logout logic here
    navigate("/login");
  };

  const toggleMobileMenu = () => {
    setIsMobileMenuOpen(!isMobileMenuOpen);
  };

  const closeMobileMenu = () => {
    setIsMobileMenuOpen(false);
  };

  return (
    <div className="flex min-h-screen bg-gray-50">
      {/* Mobile Menu Overlay */}
      {isMobileMenuOpen && (
        <div 
          className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
          onClick={closeMobileMenu}
        />
      )}

      {/* Sidebar */}
      <div className={`fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-sm border-r border-gray-200 transform transition-transform duration-300 ease-in-out lg:relative lg:translate-x-0 ${
        isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
      }`}>
        {/* Logo/Brand */}
        <div className="p-4 sm:p-6 border-b border-gray-100 flex items-center justify-between">
          <h1 className="text-xl sm:text-2xl font-bold text-blue-600">EASIL</h1>
          <button
            onClick={closeMobileMenu}
            className="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Navigation Menu */}
        <nav className="mt-4">
          <div className="px-3 sm:px-4 space-y-1">
            {menuItems.map((item) => (
              <NavLink
                key={item.path}
                to={item.path}
                onClick={closeMobileMenu}
                className={({ isActive }) =>
                  `flex items-center space-x-3 px-3 sm:px-4 py-3 rounded-lg transition-colors duration-200 text-sm sm:text-base ${
                    isActive
                      ? "bg-blue-50 text-blue-700"
                      : "text-gray-600 hover:bg-gray-50 hover:text-gray-900"
                  }`
                }
              >
                <item.icon className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
                <span className="font-medium">{item.label}</span>
              </NavLink>
            ))}
          </div>

          {/* Logout */}
          <div className="px-3 sm:px-4 mt-6 sm:mt-8">
            <button
              onClick={handleLogout}
              className="flex items-center space-x-3 px-3 sm:px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200 w-full text-sm sm:text-base"
            >
              <LogOut className="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0" />
              <span className="font-medium">Logout</span>
            </button>
          </div>
        </nav>
      </div>

      {/* Main Content */}
      <div className="flex-1 lg:ml-0">
        {/* Top Header with User Avatar */}
        <header className="bg-white shadow-sm border-b border-gray-200 px-4 sm:px-6 py-3 sm:py-4">
          <div className="flex justify-between items-center">
            <button
              onClick={toggleMobileMenu}
              className="lg:hidden p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100"
            >
              <Menu className="w-5 h-5" />
            </button>
            <div className="w-8 h-8 sm:w-10 sm:h-10 bg-gray-300 rounded-full flex items-center justify-center ml-auto">
              <span className="text-gray-600 font-semibold text-xs sm:text-sm">A</span>
            </div>
          </div>
        </header>

        {/* Page Content */}
        <main className="flex-1 p-4 sm:p-6 lg:p-8">
          <Outlet />
        </main>
      </div>
    </div>
  );
}
