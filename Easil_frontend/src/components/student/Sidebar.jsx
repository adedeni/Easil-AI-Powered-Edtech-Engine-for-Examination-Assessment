import React from "react";
import { NavLink, useNavigate } from "react-router-dom";
import { BookOpen, FileText, Award, LogOut } from "lucide-react";

const Sidebar = ({ onItemClick }) => {
  const navigate = useNavigate();

  const handleLogout = () => {
    localStorage.removeItem('userRole');
    localStorage.removeItem('username');
    navigate('/login');
    if (onItemClick) onItemClick();
  };

  const links = [
    { name: "Dashboard", path: "/student", icon: <BookOpen size={20} /> },
    { name: "Assessments", path: "/student/assessments", icon: <FileText size={20} /> },
    { name: "Results", path: "/student/results", icon: <Award size={20} /> }
  ];

  return (
    <aside className="w-64 min-h-screen bg-white shadow-md px-4 py-6">
      <h2 className="text-xl sm:text-2xl font-bold text-blue-600 mb-6 sm:mb-8">EASIL</h2>
      <nav className="space-y-2 sm:space-y-4">
        {links.map((link) => (
          <NavLink
            key={link.name}
            to={link.path}
            onClick={onItemClick}
            className={({ isActive }) =>
              `flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-md font-medium transition text-sm sm:text-base ${
                isActive ? "bg-blue-100 text-blue-700" : "text-gray-700 hover:bg-gray-100"
              }`
            }
          >
            <span className="flex-shrink-0">{link.icon}</span>
            <span>{link.name}</span>
          </NavLink>
        ))}
      </nav>
      
      {/* Logout Button */}
      <div className="mt-8 sm:mt-auto pt-6 border-t border-gray-200">
        <button
          onClick={handleLogout}
          className="flex items-center gap-3 px-3 sm:px-4 py-2 sm:py-3 rounded-md font-medium transition text-sm sm:text-base text-red-600 hover:bg-red-50 w-full"
        >
          <LogOut size={20} />
          <span>Logout</span>
        </button>
      </div>
    </aside>
  );
};

export default Sidebar;
